<?php

namespace ResponsiveImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('responsive_image');

        // Add basic configurations.
        $rootNode
            ->children()
                ->booleanNode('debug')
                    ->defaultFalse()
                ->end()
                ->variableNode('image_entity_class')
                    ->defaultValue([])
                ->end()
                ->scalarNode('image_driver')
                    ->defaultValue('gd')
                    ->validate()
                        ->ifNotInArray(array('gd', 'imagemagik'))
                        ->thenInvalid('In valid PHP image library')
                    ->end()
                ->end()
                ->scalarNode('image_compression')
                    ->defaultValue(90)
                ->end()
                ->scalarNode('image_directory')
                    ->defaultValue('images')
                ->end()
                ->scalarNode('image_styles_directory')
                    ->defaultValue('styles')
                ->end()
                ->arrayNode('breakpoints')
                    ->prototype('scalar')->end()
                ->end()
                ->variableNode('image_styles')->end()
                ->variableNode('picture_sets')->end()
                ->variableNode('crop_focus_widget')->end()
            ->end()
        ;

        // @TODO: Pictures set should be checked for valid break points and image styles.
        // @TODO: Test its working with imagemagik

        return $treeBuilder;
    }
}