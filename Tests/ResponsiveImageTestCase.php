<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Yaml\Yaml;

class ResponsiveImageTestCase extends TestCase
{
    protected $testKernel;
    protected $parameters = [];

    protected function bootSymfony()
    {
        require_once __DIR__ . '/AppKernel.php';

        $this->testKernel = new \AppKernel('test', true);
        $this->testKernel->boot();
    }

    protected function createDirectory($directory)
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    protected function deleteDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException("$directory must be a directory");
        }
        if (substr($directory, strlen($directory) - 1, 1) != '/') {
            $directory .= '/';
        }
        $files = glob($directory . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDirectory($file);
            }
            else {
                unlink($file);
            }
        }
        rmdir($directory);
    }

    protected function runCommand($name, $options = [])
    {
        if (empty($this->testKernel)) {
            $this->bootSymfony();
        }

        $application = new Application($this->testKernel);
        $application->setAutoExit(false);

        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'name' => $name,
            ]
        );
        $input->setInteractive(false);

        $exitCode = $application->run($input, $output);

        return $exitCode;
    }

    protected function getService($serviceName)
    {
        if (empty($this->testKernel)) {
            $this->bootSymfony();
        }

        $container = $this->testKernel->getContainer();

        return $container->get($serviceName);
    }

    protected function getParameters($key = '')
    {
        if (empty($this->parameters)) {
            $path             = __DIR__ . '/config_test.yml';
            $this->parameters = Yaml::parse(file_get_contents($path));
        }

        if (empty($key)) {
            return $this->parameters;
        }

        return empty($this->parameters[$key]) ? [] : $this->parameters[$key];
    }
}
