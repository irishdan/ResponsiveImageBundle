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
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineFormGenerator;
use Symfony\Component\Console\Input\InputArgument;
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
class GenerateImageEntityCrudCommand extends GenerateDoctrineCrudCommand
{
    private $formGenerator;

    protected function configure()
    {
        // @TODO: This needs to limit CRUD generation to the single entity defined in configuration
        // perhaps check the entities which implement the interface??

        $this
            ->setName('responsive_image:generate:crud')
            ->setDescription('Generates the CRUD for responsive image entity')
            ->setDefinition(
                [
                    new InputArgument(
                        'entity',
                        InputArgument::OPTIONAL,
                        'The entity class name to initialize (shortcut notation)'
                    ),
                    new InputOption(
                        'entity',
                        '',
                        InputOption::VALUE_REQUIRED,
                        'The entity class name to initialize (shortcut notation)'
                    ),
                    new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'The route prefix'),
                    new InputOption(
                        'with-write',
                        '',
                        InputOption::VALUE_NONE,
                        'Whether or not to generate create, new and delete actions'
                    ),
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
            )
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command generates a CRUD based on a Doctrine entity.

The default command only generates the list and show actions.

<info>php %command.full_name% --entity=AcmeBlogBundle:Post --route-prefix=post_admin</info>

Using the --with-write option allows to generate the new, edit and delete actions.

<info>php %command.full_name% --entity=AcmeBlogBundle:Post --route-prefix=post_admin --with-write</info>

Every generated file is based on a template. There are default templates but they can be overridden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/crud
APP_PATH/Resources/SensioGeneratorBundle/skeleton/crud</info>

And

<info>__bundle_path__/Resources/SensioGeneratorBundle/skeleton/form
__project_root__/app/Resources/SensioGeneratorBundle/skeleton/form</info>

You can check https://github.com/sensio/SensioGeneratorBundle/tree/master/Resources/skeleton
in order to know the file structure of the skeleton
EOT
            );
    }

    protected function createGenerator($bundle = null)
    {
        parent::createGenerator($bundle);
    }

    protected function getFormGenerator($bundle = null)
    {
        if (null === $this->formGenerator) {
            $this->formGenerator = new DoctrineFormGenerator($this->getContainer()->get('filesystem'));

            // @TODO: Switch to this bundles skeleton directory.
            $skeletonDirectory = $this->getSkeletonDirs($bundle);
            var_dump($skeletonDirectory);
            $this->formGenerator->setSkeletonDirs($skeletonDirectory);
        }

        return $this->formGenerator;
    }
}