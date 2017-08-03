<?php

namespace IrishDan\ResponsiveImageBundle\Test\Command;

use IrishDan\ResponsiveImageBundle\Tests\ResponsiveImageTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateImageEntityCommandTest extends ResponsiveImageTestCase
{
    private $application;

    protected function setUp()
    {
        $this->bootSymfony();
        $this->application = new Application($this->testKernel);
        $this->application->setAutoExit(false);
    }

    public function testCommandRunsSuccessfully()
    {
        $output = new NullOutput();
        $input = new ArrayInput([
                'name' => 'responsive_image:generate:entity',
                '--bundle' => 'ResponsiveImageBundle',
                '--entity_name' => 'Img',
            ]
        );
        $input->setInteractive(false);

        $exitCode = $this->application->run($input, $output);

        $this->assertSame(1, $exitCode);
    }
}