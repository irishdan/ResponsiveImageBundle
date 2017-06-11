<?php

namespace IrishDan\ResponsiveImageBundle\DependencyInjection;

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

        // Set as parameter for easier passing.
        $container->setParameter('responsive_image', $config);

        // Create the image directories as parameters for routing.
        $container->setParameter('responsive_image.image_directory', $config['image_directory']);
        $container->setParameter('responsive_image.image_styles_directory', $config['image_styles_directory']);

        // Create the image_entity_class parameter.
        if (!empty($config['image_entity_class'])) {
            $container->setParameter('responsive_image.entity_class', $config['image_entity_class']);
        } else {
            $container->setParameter('responsive_image.entity_class', 'AppBundle\Entity\Image');
        }

        // Add the cropfocus.html.twig to form resources
        $resources = $container->getParameter('twig.form.resources');
        $container->setParameter('twig.form.resources', array_merge(['ResponsiveImageBundle::cropfocus.html.twig'], $resources));
    }
}