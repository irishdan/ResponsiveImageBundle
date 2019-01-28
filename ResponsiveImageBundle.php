<?php

namespace IrishDan\ResponsiveImageBundle;

use IrishDan\ResponsiveImageBundle\DependencyInjection\Compiler\DefaultConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ResponsiveImageBundle
 *
 * @package IrishDan\ResponsiveImageBundle
 */
class ResponsiveImageBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        // $container->addCompilerPass(new DefaultConfigurationPass());
    }
}
