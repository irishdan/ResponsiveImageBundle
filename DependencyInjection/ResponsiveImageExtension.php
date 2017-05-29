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

        if (!empty($config['aws_s3'])) {
            $config['path_prefix'] = $this->buildPathPrefix($config);
        }

        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

        // Set as parameter for easier passing.
        $container->setParameter('responsive_image', $config);

        // Create the image directories as parameters for routing.
        $container->setParameter('image_directory', $config['image_directory']);
        $container->setParameter('image_styles_directory', $config['image_styles_directory']);

        // Create the image_entity_class parameter.
        $container->setParameter('image_entity_class', $config['image_entity_class']);

        // Add the cropfocus.html.twig to form resources
        $resources = $container->getParameter('twig.form.resources');
        $container->setParameter('twig.form.resources', array_merge(['ResponsiveImageBundle::cropfocus.html.twig'], $resources));

        // Add the aws_s3 config as a parameter.
        if (!empty($config['aws_s3'])) {
            $container->setParameter('responsive_image.aws_s3', $config['aws_s3']);
        }
    }

    protected function buildPathPrefix($config)
    {
        $pathPrefix = null;
        if (!empty($config['aws_s3']) && !empty($config['aws_s3']['enabled'])) {
            $url = $config['aws_s3']['protocol'] . '://s3-' . $config['aws_s3']['region'] . '.amazonaws.com/' . $config['aws_s3']['bucket'] . '/' . $config['aws_s3']['directory'] . '/';
            $pathPrefix = $url;
        }

        return $pathPrefix;
    }
}