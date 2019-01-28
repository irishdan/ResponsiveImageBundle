<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Command;

use IrishDan\ResponsiveImageBundle\ImageEntityClassLocator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;


/**
 * Class CreateImageEntityCommand
 *
 * @package IrishDan\ResponsiveImageBundle\Command
 */
class GenerateImageEntityCrudCommand extends Command
{
    protected $responsiveImageEntity;
    protected $imageEntityShorthand;
    protected $entityName;
    protected $bundle;
    protected $doctrine;
    protected $entityShortNotation;
    protected $metadata;

    public function __construct(ImageEntityClassLocator $entityClassFinder, $doctrine)
    {
        parent::__construct();

        $this->responsiveImageEntity = $entityClassFinder->getClassName();
        $this->doctrine              = $doctrine;
        $em                          = $this->doctrine->getManager();

        try {
            $this->metadata = $em->getClassMetadata($this->responsiveImageEntity);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf(
                    'Entity "%s" does not exist. Create it with the "doctrine:generate:entity" command and then execute this command again.',
                    $this->responsiveImageEntity
                )
            );
        }

        $namespace = $this->metadata->namespace;

        // This is bit hacky but it'll do for now.
        // Lets get rid of the '\Entity'.
        if (strpos($namespace, '\\Entity') > 0) {
            $namespace = substr($namespace, 0, -7);
        }

        $namespaceParts            = explode('\\', $namespace);
        $this->bundle              = array_pop($namespaceParts);
        $entityNameParts           = explode('\\', $this->responsiveImageEntity);
        $this->entityName          = array_pop($entityNameParts);
        $this->entityShortNotation = $this->bundle . ':' . $this->entityName;
    }

    protected function configure()
    {
        // This limits CRUD generation to the single entity defined in configuration

        $this
            ->setName('responsive_image:generate:crud')
            ->setDescription('Generates the CRUD for responsive image entity')
            ->setDefinition(
                [
                    new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'The route prefix'),
                    new InputOption(
                        'format',
                        '',
                        InputOption::VALUE_REQUIRED,
                        'The format used for configuration files (php, xml, yml, or annotation)',
                        'annotation'
                    ),
                    new InputOption(
                        'overwrite',
                        '',
                        InputOption::VALUE_NONE,
                        'Overwrite any existing controller or form class when generating the CRUD contents'
                    ),
                ]
            );
    }

    /**
     * @see Command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $entity = Validators::validateEntityName($this->entityShortNotation);
        $bundle = $this->bundle;

        $format = Validators::validateFormat($input->getOption('format'));
        $prefix = $this->getRoutePrefix($input, $entity);

        $questionHelper->writeSection($output, 'CRUD generation');
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        $generator = $this->getGenerator($bundle);

        // $withWrite = true;
        // $forceOverwrite = true;
        // @TODO: Perhaps Don't force overwrite
        $generator->generate($bundle, $this->entityName, $this->metadata[0], $format, $prefix, true, true);

        $output->writeln('Generating the CRUD code: <info>OK</info>');

        $errors = [];
        $runner = $questionHelper->getRunner($output, $errors);

        // routing
        $output->write('Updating the routing: ');
        if ('annotation' != $format) {
            $runner($this->updateRouting($questionHelper, $input, $output, $bundle, $format, $entity, $prefix));
        }
        else {
            $runner($this->updateAnnotationRouting($bundle, $entity, $prefix));
        }

        $questionHelper->writeGeneratorSummary($output, $errors);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Doctrine2 CRUD generator');

        // namespace
        $output->writeln(
            [
                '',
                'This command helps you generate CRUD controllers and templates.',
                '',
                'First, give the name of the existing entity for which you want to generate a CRUD',
                '(use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>)',
                '',
            ]
        );

        $entity = $this->entityName;
        $bundle = $this->bundle;
        try {
            $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle) . '\\' . $entity;
            $this->getEntityMetadata($entityClass);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf(
                    'Entity "%s" does not exist in the "%s" bundle. You may have mistyped the bundle name or maybe the entity doesn\'t exist yet (create it first with the "doctrine:generate:entity" command).',
                    $entity,
                    $bundle
                )
            );
        }

        // format
        $format = $input->getOption('format');
        $output->writeln(
            [
                '',
                'Determine the format to use for the generated CRUD.',
                '',
            ]
        );
        $question = new Question(
            $questionHelper->getQuestion('Configuration format (yml, xml, php, or annotation)', $format), $format
        );
        $question->setValidator(['Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat']);
        $format = $questionHelper->ask($input, $output, $question);
        $input->setOption('format', $format);

        // route prefix
        $prefix = $this->getRoutePrefix($input, $entity);
        $output->writeln(
            [
                '',
                'Determine the routes prefix (all the routes will be "mounted" under this',
                'prefix: /prefix/, /prefix/new, ...).',
                '',
            ]
        );
        $prefix = $questionHelper->ask(
            $input,
            $output,
            new Question($questionHelper->getQuestion('Routes prefix', '/' . $prefix), '/' . $prefix)
        );
        $input->setOption('route-prefix', $prefix);

        // summary
        $output->writeln(
            [
                '',
                $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
                '',
                sprintf('You are going to generate a CRUD controller for "<info>%s:%s</info>"', $bundle, $entity),
                sprintf('using the "<info>%s</info>" format.', $format),
                '',
            ]
        );
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = [];

        if (is_dir(
            $dir = $this->getContainer()->get('kernel')->getRootdir() . '/Resources/ResponsiveImageBundle/skeleton'
        )) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = __DIR__ . '/../Resources/skeleton';

        return $skeletonDirs;
    }
}
