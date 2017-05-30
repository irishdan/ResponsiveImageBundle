<?php

namespace ResponsiveImageBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Yaml\Yaml;

class ResponsiveImageTestCase extends WebTestCase
{
    private $testKernel;
    private $parameters = [];

    protected function bootSymfony()
    {
        require_once __DIR__ . '/AppKernel.php';

        $this->testKernel = new \AppKernel('test', true);
        $this->testKernel->boot();
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
            $path = __DIR__ . '/config_test.yml';
            $this->parameters = Yaml::parse(file_get_contents($path));
        }

        if (empty($key)) {
            return $this->parameters;
        }

        return empty($this->parameters[$key]) ? [] : $this->parameters[$key];
    }
}