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
    protected $responsiveImageEntity;
    protected $bundle = 'AppBundle';
    protected $entityName = 'Image';

    public function __construct(ImageEntityNameResolver $entityNameResolver)
    {
        parent::__construct();

        $this->responsiveImageEntity = $entityNameResolver->getClassName();
    }

    private function imageEntityExists()
    {
        return !empty($this->responsiveImageEntity);
    }

    protected function configure()
    {
        // Limit generation to One entity for now!
        // Either:
        // - Check the image entity configuration, if its set, use that in the interaction validation
        //   to limit
        //   If its not set warn users that it needs to be set after the entity has been generated.

        // @TODO: Would be good to ask for additional fields.

        $this
            ->setName('responsive_image:generate:entity')
            ->setDescription('Creates the ResponsiveImage entity, ' . $this->responsiveImageEntity);

        if ($this->imageEntityExists()) {
            // $this
            //     ->setDefinition(
            //         [
            //             new InputOption(
            //                 'override', '', InputOption::VALUE_REQUIRED, 'Override if entity already exists'
            //             ),
            //         ]
            //     );
        }
        else {
            $this
                ->setDefinition(
                    [
                        new InputOption('bundle', '', InputOption::VALUE_REQUIRED, 'The bundle for this entity'),
                        new InputOption('entity_name', '', InputOption::VALUE_REQUIRED, 'The name of the entity'),
                    ]
                );
        }
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

        // Get the Bundle to generate it in
        if ($this->imageEntityExists()) {
            $message[] = sprintf('It looks like the image entity exists as %s', $this->responsiveImageEntity);
            $message[] = '';
            $message[] = sprintf(
                'Therefore this generator will overwrite that class, %s',
                $this->responsiveImageEntity
            );

            $entityPart = explode('\\', $this->responsiveImageEntity);

            // @TODO: Make sure to set this as a valid bundle
            $this->bundle     = $entityPart[0];
            $this->entityName = $entityPart[2];
        }
        else {
            $message[] = sprintf('If looks like there is no image entity.');
        }


        $output->writeln($message);

        if (!$this->imageEntityExists()) {
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
            $this->getContainer()->get('filesystem')
        );
    }
}