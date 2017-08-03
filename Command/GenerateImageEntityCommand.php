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
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('responsive_image:generate:entity')
            ->setDescription('Create a new responsive image doctrine entity')
            ->setDefinition(
                [
                    new InputOption('bundle', '', InputOption::VALUE_REQUIRED, 'The bundle for this entity'),
                    new InputOption('entity_name', '', InputOption::VALUE_REQUIRED, 'The name of the entity'),
                ]
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Image entity generator');

        // Get the Bundle to generate it in
        $output->writeln(
            [
                'This command helps you generate a doctrine image entity class',
                '',
                'First, give the name of the bundle to generate the image in (eg <comment>AppBundle</comment>)',
            ]
        );

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

        $bundle = $input->getOption('bundle');
        $name   = $input->getOption('entity_name');

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