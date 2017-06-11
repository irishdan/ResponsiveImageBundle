<?php

namespace IrishDan\ResponsiveImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package ResponsiveImageBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * The root configuration for responsive image bundle.
     *
     * @return TreeBuilder
     */
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
                ->booleanNode('cache_bust')
                    ->defaultFalse()
                ->end()
                ->variableNode('image_entity_class')
                    ->defaultValue([])
                ->end()
                ->enumNode('image_driver')
                    ->defaultValue('gd')
                    ->values(['gd', 'imagick'])
                ->end()
                ->integerNode('image_compression')
                    ->defaultValue(90)
                    ->min(0)->max(100)
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
                ->arrayNode('image_styles')
                    ->prototype('array')
                        ->children()
                            ->integerNode('width')
                                ->min(1)
                            ->end()
                            ->integerNode('height')
                                ->min(1)
                            ->end()
                            ->enumNode('effect')
                                ->defaultValue('scale')
                                ->values(['scale', 'crop'])
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->variableNode('picture_sets')->end()
                ->arrayNode('crop_focus_widget')
                    ->children()
                        ->booleanNode('include_js_css')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('display_coordinates')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}