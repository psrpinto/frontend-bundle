<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        return $treeBuilder
            ->root('rj_frontend', 'array')
                ->children()
                    ->arrayNode('livereload')
                        ->canBeEnabled()
                        ->children()
                            ->scalarNode('url')->defaultValue('/livereload.js?port=37529')->end()
                        ->end()
                    ->end()
                    ->arrayNode('packages')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('path_prefix')->defaultNull()->end()
                                ->arrayNode('url_prefix')
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(function ($v) { return array($v); })
                                    ->end()
                                    ->defaultValue(array())
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('manifest')
                                    ->canBeEnabled()
                                    ->children()
                                        ->enumNode('format')
                                            ->values(array('json', 'yaml'))
                                            ->defaultValue('json')
                                        ->end()
                                        ->scalarNode('path')->isRequired()->end()
                                        ->scalarNode('root_key')->defaultNull()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
