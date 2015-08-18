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
        return $this->createRoot('rj_frontend', 'array')
            ->children()
                ->arrayNode('fallback_patterns')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('.*bundles\/.*'))
                ->end()
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
        ->end();
    }

    private function addLivereloadSection()
    {
        return $this->createRoot('livereload')
            ->canBeEnabled()
            ->children()
                ->scalarNode('url')
                    ->defaultValue('/livereload.js?port=37529')
                ->end()
            ->end()
        ;
    }

    private function addPackagePrefixesSection()
    {
        return $this->createRoot('prefixes')
            ->prototype('scalar')->end()
            ->defaultValue(array(null))
            ->beforeNormalization()
                ->ifString()
                ->then(function ($v) { return array($v); })
            ->end()
        ;
    }

    private function addPackageManifestSection()
    {
        return $this->createRoot('manifest')
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
    }

    private function createRoot($root, $type = null)
    {
        $treeBuilder = new TreeBuilder();

        if ($type !== null) {
            return $treeBuilder->root($root, $type);
        }

        return $treeBuilder->root($root);
    }
}
