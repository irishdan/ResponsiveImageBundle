<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace IrishDan\ResponsiveImageBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DefaultConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // $definition = $container->findDefinition('responsive_image_filesystem_x');
        // $definition->addMethodCall('debug', array('Logger CREATED!'));
    }
}