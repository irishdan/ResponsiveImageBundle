<?php

namespace ResponsiveImageBundle\DependencyInjection;


use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResponsiveImageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }
        
        // Set defaults from config.yml.
        $container->setParameter('responsive_image', $config);

        // Create the image directories as parameters for routing.
        $container->setParameter('image_directory', $config['image_directory']);
        $container->setParameter('image_styles_directory', $config['image_styles_directory']);

        // Create the image_entity_class parameter.
        $container->setParameter('image_entity_class', $config['image_entity_class']);

        // Add the cropfocus.html.twig to form resources
        $resources = $container->getParameter('twig.form.resources');
        $container->setParameter('twig.form.resources', array_merge(array('ResponsiveImageBundle::cropfocus.html.twig'), $resources));

        // Add the aws_s3 config as a parameter.
        // @TODO: Add validation etc for this config
        $container->setParameter('responsive_image.aws_s3', $config['aws_s3']);

    }
}