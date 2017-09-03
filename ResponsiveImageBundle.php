<?php
/**
 * This file is part of the IrishDan\ResponsiveImageBundle package.
 *
 * (c) Daniel Byrne <danielbyrne@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

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