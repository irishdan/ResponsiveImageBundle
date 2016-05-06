<?php

namespace ResponsiveImageBundle\DependencyInjection;

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
     * The root configuration for repsonsive image bundle.
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
                ->variableNode('image_styles')->end()
                ->variableNode('picture_sets')->end()
                ->variableNode('crop_focus_widget')->end()
                ->append($this->addAWSNode())
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * The configuration for responsive image AWS functionality.
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function addAWSNode() {
        $builder = new TreeBuilder();
        $node = $builder->root('aws_s3');

        $node
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                ->end()
                ->enumNode('local_file_policy')
                    ->defaultValue('KEEP_NONE')
                    ->values(['KEEP_ALL', 'KEEP_NONE', 'KEEP_ORIGINAL'])
                ->end()
                ->enumNode('remote_file_policy')
                   ->defaultValue('ALL')
                   ->values(['ALL', 'STYLED_ONLY'])
                ->end()
                ->scalarNode('temp_directory')
                    ->defaultValue(null)
                ->end()
                ->scalarNode('bucket')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('region')
                    ->defaultValue('eu-west-1')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('version')
                    ->defaultValue('latest')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('directory')
                    ->defaultValue('')
                ->end()
                ->enumNode('protocol')
                    ->defaultValue('http')
                    ->values(['http', 'https'])
                ->end()
                ->scalarNode('access_key_id')->end()
                ->scalarNode('secret_access_key')->end()
            ->end()
        ;

        return $node;
    }
}