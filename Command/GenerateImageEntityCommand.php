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

use IrishDan\ResponsiveImageBundle\Generator\ImageEntityGenerator;
use IrishDan\ResponsiveImageBundle\ImageEntityNameResolver;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;


/**
 * Class CreateImageEntityCommand
 *
 * @package IrishDan\ResponsiveImageBundle\Command
 */
class GenerateImageEntityCommand extends GeneratorCommand
{
    protected $entityNameResolver;
    protected $responsiveImageEntity;
    protected $bundle;
    protected $entityName;
    protected $doctrine;
    protected $classExists;
    protected $needsOverWritePermission = false;
    protected $overwrite = false;

    public function __construct(ImageEntityNameResolver $entityNameResolver, $doctrine)
    {
        $this->entityNameResolver    = $entityNameResolver;
        $this->doctrine              = $doctrine;
        $this->responsiveImageEntity = $entityNameResolver->getClassName();
        $this->classExists           = $entityNameResolver->classExists();

        parent::__construct();
    }

    protected function configure()
    {
        // Limit generation to One entity for now!
        // We are checking two things:
        // If entity class name is set
        // If the class exists

        // @TODO: Would be good to ask for additional fields.

        $this
            ->setName('responsive_image:generate:entity')
            ->setDescription('Creates the Responsive Image entity, ' . $this->responsiveImageEntity);

        // If the Classname is not set we need to ask for it
        if (empty($this->responsiveImageEntity)) {
            $this
                ->setDefinition(
                    [
                        new InputOption('bundle', '', InputOption::VALUE_REQUIRED, 'The bundle for this entity'),
                        new InputOption('entity_name', '', InputOption::VALUE_REQUIRED, 'The name of the entity'),
                    ]
                );
        }
        // The classname is set and and the entity already exists
        elseif ($this->classExists) {
            $this->needsOverWritePermission = true;

            // Needs to have the entityName and bundle properties set
            // @TODO:Is there a nicer way to do this?
            $em        = $this->doctrine->getManager();
            $metadata  = $em->getClassMetadata($this->responsiveImageEntity);
            $namespace = $metadata->namespace;

            // This is bit hacky but it'll do for now.
            // Lets get rid of the '\Entity'.
            if (strpos($namespace, '\\Entity') > 0) {
                $namespace = substr($namespace, 0, -7);
            }

            $namespaceParts   = explode('\\', $namespace);
            $this->bundle     = array_pop($namespaceParts);
            $entityNameParts  = explode('\\', $this->responsiveImageEntity);
            $this->entityName = array_pop($entityNameParts);

            $this
                ->setDefinition(
                    [
                        new InputOption(
                            'overwrite', '', InputOption::VALUE_NONE, 'Overwrite the existing image entity'
                        ),
                    ]
                );
        }
        // The classname is set but the entity does not exist.
        // We don't need any options for this, simple generate based on the defined classname
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Image entity generator');

        $message = [
            'This Currently only supports one Image entity',
            '',
        ];

        // If entity already exists,
        // get overwrite permission
        if ($this->needsOverWritePermission) {
            $message[] = sprintf('It looks like the image entity exists as %s', $this->responsiveImageEntity);
            $message[] = '';
            $message[] = sprintf(
                'Therefore this generator will overwrite that class, %s',
                $this->responsiveImageEntity
            );

            $output->writeln($message);

            // Ask whether overwrite is allowed
            $question  = $this->createYesNoQuestion($questionHelper, $input, $this->responsiveImageEntity);
            $overwrite = $questionHelper->ask($input, $output, $question);

            if ($overwrite !== 'y') {
                throw new \RuntimeException(
                    'Aborting, overwrite permission is needed.'
                );
            }
            else {
                $this->overwrite = true;
            }
        }
        // If class name is not set,
        // Ask the bundle and the entity name questions
        elseif (empty($this->responsiveImageEntity)) {
            $question = new Question(
                $questionHelper->getQuestion('The bundle name', $input->getOption('bundle')),
                $input->getOption('bundle')
            );

            $question->setValidator(['Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleName']);
            $question->setNormalizer(
                function ($value) {
                    return $value ? trim($value) : '';
                }
            );
            $question->setMaxAttempts(2);

            $bundle = $questionHelper->ask($input, $output, $question);
            $input->setOption('bundle', $bundle);

            // Get the Bundle to generate it in
            $output->writeln(
                [
                    '',
                    'Now, give the name of the new entity class (eg <comment>Image</comment>)',
                ]
            );

            // Get the new class name and validate it.
            $question = new Question(
                $questionHelper->getQuestion('The entity name', $input->getOption('entity_name')),
                $input->getOption('entity_name')
            );
            $question->setValidator(
                function ($answer) {
                    // Should only contain letters.
                    $valid = preg_match('/^[a-zA-Z]+$/', $answer);
                    if (!$valid) {
                        throw new \RuntimeException(
                            'The class name should only contain letters'
                        );
                    }

                    return $answer;
                }
            );
            $question->setNormalizer(
                function ($value) {
                    return $value ? trim($value) : '';
                }
            );

            $notificationName = $questionHelper->ask($input, $output, $question);
            $input->setOption('entity_name', $notificationName);
        }
        // Should be all ready to generate

        // At the end we need the bundle and the entity
        if (empty($this->entityName) || empty($this->bundle)) {
            throw new \RuntimeException(
                'Required options have not been set'
            );
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion(
                $questionHelper->getQuestion('Do you confirm generation', 'yes', '?'),
                true
            );
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $style = new SymfonyStyle($input, $output);

        $bundle = $this->bundle;
        $name   = $this->entityName;

        $style->text('Generating New doctrine entity class ' . $name . ' generated in ' . $bundle);

        try {
            $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
        }

        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, $name);

        $output->writeln(sprintf('Generated the <info>%s</info> entity in <info>%s</info>', $name, $bundle->getName()));
        $questionHelper->writeGeneratorSummary($output, []);
    }

    /**
     * @return ImageEntityGenerator
     */
    protected function createGenerator()
    {
        return new ImageEntityGenerator(
            $this->getContainer()->get('filesystem'),
            $this->overwrite
        );
    }

    protected function createYesNoQuestion($questionHelper, $input, $entity)
    {
        $question = new Question(
            $questionHelper->getQuestion(
                'Overwrite the existing image entity ' . $entity . '? <comment>[yes]</comment>',
                'overwrite'
            ), 'yes'
        );
        $question->setNormalizer(
            function ($value) {
                return $value[0] == 'y' ? 'y' : 'n';
            }
        );
        $question->setValidator(
            function ($answer) {
                // Should only contain letters.
                $allowed = [
                    'y',
                    'n',
                ];
                $valid   = in_array($answer, $allowed);
                if (!$valid) {
                    throw new \RuntimeException(
                        'Only allowed values are ' . implode(', ', $allowed)
                    );
                }

                return $answer;
            }
        );

        return $question;
    }
}