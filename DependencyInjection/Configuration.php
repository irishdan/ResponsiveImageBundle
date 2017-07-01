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
            ->validate()
                ->ifTrue(function($config) {
                    return $this->validateBreakpointKeys($config);
                })
                ->thenInvalid('Undefined breakpoint key detected in picture_set or size_set definitions')
            ->end()
            ->validate()
                ->ifTrue(function($config) {
                    return $this->validateStyleKeys($config);
                })
                ->thenInvalid('Undefined style key detected in picture_set or size_set definitions')
            ->end()
            ->children()
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
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('media_query')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('image_styles')
                    ->useAttributeAsKey('name')
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
                            ->booleanNode('greyscale')
                                ->defaultFalse()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('picture_sets')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('fallback')->end()
                            ->booleanNode('multipliers')->end()
                            ->arrayNode('sources')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('size_sets')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('fallback')->end()
                            ->arrayNode('sizes')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('breakpoint')->end()
                                        ->scalarNode('calc')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('srcsets')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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

    private function validateStyleKeys($config) {
        // Get a list of image styles and break points to validate against.
        $styles = [];
        if (!empty($config['image_styles'])) {
            $styles = array_keys($config['image_styles']);
        }

        foreach ($config['picture_sets'] as $pictureSet) {
            foreach ($pictureSet['sources'] as $breakpoint => $style) {
                if (!in_array($style, $styles)) {
                    return true;
                }
            }
        }

        foreach ($config['size_sets'] as $sizeSet) {
            foreach ($sizeSet['srcsets'] as $style) {
                if (!in_array($style, $styles)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function validateBreakpointKeys($config) {
        // Get a list of breakpoints to validate against.
        $breakpoints = [];
        if (!empty($config['breakpoints'])) {
            $breakpoints = array_keys($config['breakpoints']);
        }

        // Validate that the picture sets are defined using valid breakpoints
        foreach ($config['picture_sets'] as $pictureSet) {
            foreach ($pictureSet['sources'] as $breakpoint => $style) {
                if (!in_array($breakpoint, $breakpoints)) {
                    return true;
                }
            }
        }

        // Validate that the picture sets are defined using valid breakpoints
        foreach ($config['size_sets'] as $sizeSet) {
            foreach ($sizeSet['sizes'] as $size) {
                $breakpoint = empty($size['breakpoint']) ? null : $size['breakpoint'];
                if ($breakpoint) {
                    if (!in_array($breakpoint, $breakpoints)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}