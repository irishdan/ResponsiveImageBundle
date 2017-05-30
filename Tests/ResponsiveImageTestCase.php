<?php

namespace ResponsiveImageBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Yaml\Yaml;

class ResponsiveImageTestCase extends WebTestCase
{
    private $kernel;
    private $parameters = [];
    private $service;

    protected function bootSymfony()
    {
        require_once __DIR__ . 'AppKernel.php';

        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();
    }

    protected function setService($serviceName)
    {
        if (empty($this->kernel)) {
            $this->bootSymfony();
        }

        $container = $this->kernel->getContainer();
        $this->service = $container->get($serviceName);
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