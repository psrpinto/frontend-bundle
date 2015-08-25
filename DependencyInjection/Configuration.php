<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Rj\FrontendBundle\Util\Util;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const DEFAULT_PREFIX = 'assets';

    private $kernelRootDir;

    public function __construct($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $self = $this;

        return $this->createRoot('rj_frontend', 'array')
            ->children()
                ->booleanNode('override_default_package')->defaultTrue()->end()
                ->arrayNode('fallback_patterns')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('.*bundles\/.*'))
                ->end()
                ->append($this->addLivereloadSection())
                ->append($this->addPackagePrefixSection(self::DEFAULT_PREFIX))
                ->append($this->addPackageManifestSection())
                ->arrayNode('packages')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->append($this->addPackagePrefixSection())
                            ->append($this->addPackageManifestSection())
                        ->end()
                        ->beforeNormalization()
                            ->ifTrue(function ($config) use ($self) {
                                return $self->mustApplyManifestDefaultPath($config);
                            })
                            ->then(function ($config) use ($self) {
                                return $self->applyManifestDefaultPath($config);
                            })
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function ($config) {
                            return in_array('default', array_keys($config));
                        })
                        ->thenInvalid("'default' is a reserved package name")
                    ->end()
                ->end()
            ->end()
            ->beforeNormalization()
                ->ifTrue(function ($config) use ($self) {
                    return $self->mustApplyManifestDefaultPath($config);
                })
                ->then(function ($config) use ($self) {
                    return $self->applyManifestDefaultPath($config);
                })
            ->end()
        ->end();
    }

    private function addLivereloadSection()
    {
        return $this->createRoot('livereload')
            ->canBeEnabled()
            ->children()
                ->scalarNode('url')
                    ->defaultValue('//localhost:35729/livereload.js')
                ->end()
            ->end()
        ;
    }

    private function addPackagePrefixSection($defaultValue = null)
    {
        $node = $this->createRoot('prefix')
            ->prototype('scalar')->end()
            ->defaultValue(array($defaultValue))
            ->cannotBeEmpty()
            ->beforeNormalization()
                ->ifString()
                ->then(function ($v) { return array($v); })
            ->end()
            ->validate()
                ->ifTrue(function ($prefixes) {
                    return Util::containsUrl($prefixes)
                        && Util::containsNotUrl($prefixes);
                })
                ->thenInvalid('Packages cannot have both URL and path prefixes')
            ->end()
            ->validate()
                ->ifTrue(function ($prefixes) {
                    return count($prefixes) > 1
                        && Util::containsNotUrl($prefixes);
                })
                ->thenInvalid('Packages can only have one path prefix')
            ->end()
        ;

        return $defaultValue === null
            ? $node->isRequired()
            : $node
        ;
    }

    private function addPackageManifestSection()
    {
        return $this->createRoot('manifest')
            ->canBeEnabled()
            ->children()
                ->scalarNode('format')
                    ->defaultValue('json')
                    ->validate()
                        ->ifNotInArray(array('json'))
                        ->thenInvalid('For the moment only JSON manifest files are supported')
                    ->end()
                ->end()
                ->scalarNode('path')->isRequired()->end()
                ->scalarNode('root_key')->defaultNull()->end()
            ->end()
            ->beforeNormalization()
                ->ifString()
                ->then(function ($v) { return array('enabled' => true, 'path' => $v); })
            ->end()
        ;
    }

    /**
     * Returns true if the manifest's path has not been defined AND:
     *  - a prefix has not been defined
     *  - OR if a prefix has been defined, it's not a URL
     *
     * Note that the manifest's configuration can be a string, in which case it
     * represents the path to the manifest file.
     *
     * This method is public because of the inability to use $this in closures
     * in PHP 5.3
     *
     * @param  array   $config
     * @return boolean
     */
    public function mustApplyManifestDefaultPath($config)
    {
        return isset($config['manifest']) &&
            !is_string($config['manifest']) &&
            !isset($config['manifest']['path']) &&
            (!isset($config['prefix']) || !Util::containsUrl($config['prefix']))
        ;
    }

    /**
     * Apply a default manifest path computed from the defined prefix.
     *
     * After calling this method, the manifest's path will be
     * %kernel.root_dir%/../web/$prefix/manifest.json, where $prefix is the
     * configured prefix.
     *
     * Note that this method is used for both the default package's config and
     * for each custom package's config.
     *
     * This method is public because of the inability to use $this in closures
     * in PHP 5.3
     *
     * @param  array $config
     * @return array
     */
    public function applyManifestDefaultPath($config)
    {
        $prefix = isset($config['prefix']) ? $config['prefix'] : self::DEFAULT_PREFIX;

        if (is_array($prefix)) {
            $prefix = $prefix[0];
        }

        if (!is_array($config['manifest'])) {
            $config['manifest'] = array('enabled' => true);
        }

        $config['manifest']['path'] = implode(DIRECTORY_SEPARATOR, array(
            $this->kernelRootDir,
            '..',
            'web',
            $prefix,
            'manifest.json',
        ));

        return $config;
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
