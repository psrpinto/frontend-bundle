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
                    ->append($this->addLivereloadSection())
                    ->arrayNode('packages')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->append($this->addPackagePrefixesSection())
                                ->append($this->addPackageManifestSection())
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addLivereloadSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('livereload');

        $node
            ->canBeEnabled()
            ->children()
                ->scalarNode('url')->defaultValue('/livereload.js?port=37529')->end()
            ->end()
        ;

        return $node;
    }

    private function addPackagePrefixesSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('prefixes');

        $node
            ->prototype('scalar')->end()
            ->defaultValue(array(null))
            ->beforeNormalization()
                ->ifString()
                ->then(function ($v) { return array($v); })
            ->end()
        ;

        return $node;
    }

    private function addPackageManifestSection()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('manifest');

        $node
            ->canBeEnabled()
            ->children()
                ->enumNode('format')
                    ->values(array('json', 'yaml'))
                    ->defaultValue('json')
                ->end()
                ->scalarNode('path')->isRequired()->end()
                ->scalarNode('root_key')->defaultNull()->end()
            ->end()
        ;

        return $node;
    }
}
