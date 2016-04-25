<?php

namespace ResponsiveImageBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;


class ResponsiveImageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // $configuration = new Configuration();
        // $config = $this->processConfiguration($configuration, $configs);
        // $loader = new YamlFileLoader(
        //     $container,
        //     new FileLocator(__DIR__.'/../Resources/config')
        // );
        // $loader->load('images.yml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

        // Set defaults from config.yml.
        $container->setParameter('responsive_image', []);
        foreach (['defaults', 'object_name'] as $attribute) {
            if (empty($config[$attribute])) {
                $config[$attribute] = NULL;
            }
        }
        $container->setParameter('responsive_image', $config);

        // Create the image_styles_directory for routing.
        $container->setParameter('image_styles_directory', $config['image_styles_directory']);

        // Create the image_entity_class paramater.
        $container->setParameter('image_entity_class', $config['image_entity_class']);

    }
}