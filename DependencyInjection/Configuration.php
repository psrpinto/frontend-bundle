<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
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
                        ->canBeDisabled()
                        ->children()
                            ->scalarNode('url')->defaultValue('/livereload.js?port=37529')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
