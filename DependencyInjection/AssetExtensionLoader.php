<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Rj\FrontendBundle\Util\Util;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class AssetExtensionLoader
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @param string $alias
     * @param ContainerBuilder $container
     */
    public function __construct($alias, ContainerBuilder $container)
    {
        $this->alias = $alias;
        $this->container = $container;
    }

    /**
     * @param array $config
     * @param LoaderInterface $loader
     */
    public function load(array $config, LoaderInterface $loader)
    {
        $loader->load('asset.yml');

        if ($config['override_default_package']) {
            $loader->load('fallback.yml');

            $defaultPackage = $this->createPackage('default', array(
                'prefix'   => $config['prefix'],
                'manifest' => $config['manifest'],
            ));

            $defaultPackageId = $this->getPackageId('default');
            $this->container->setDefinition($defaultPackageId, $defaultPackage);

            $fallbackPackage = $this->createFallbackPackage(
                $config['fallback_patterns'],
                new Reference($defaultPackageId)
            );

            $this->container->setDefinition($this->namespaceService('package.fallback'), $fallbackPackage);
        }

        foreach ($config['packages'] as $name => $packageConfig) {
            $packageTag = $this->namespaceService('package.asset');
            $package = $this->createPackage($name, $packageConfig)
                ->addTag($packageTag, array('alias' => $name));

            $this->container->setDefinition($this->getPackageId($name), $package);
        }
    }

    /**
     * @param string $name
     * @param array $config
     * @return Definition
     */
    private function createPackage($name, array $config)
    {
        $prefixes = $config['prefix'];
        $isUrl = Util::containsUrl($prefixes);

        $packageDefinition = $isUrl
            ? new DefinitionDecorator($this->namespaceService('asset.package.url'))
            : new DefinitionDecorator($this->namespaceService('asset.package.path'))
        ;

        return $packageDefinition
            ->addArgument($isUrl ? $prefixes : $prefixes[0])
            ->addArgument($this->createVersionStrategy($name, $config['manifest']))
            ->setPublic(false);
    }

    /**
     * @param array $patterns
     * @param Reference $customDefaultPackage
     * @return Definition
     */
    private function createFallbackPackage(array $patterns, Reference $customDefaultPackage)
    {
        $packageDefinition = new DefinitionDecorator($this->namespaceService('asset.package.fallback'));

        return $packageDefinition
            ->setPublic(false)
            ->addArgument($patterns)
            ->addArgument($customDefaultPackage);
    }

    /**
     * @param string $name
     * @return string
     */
    private function getPackageId($name)
    {
        return $this->namespaceService("_package.$name");
    }

    /**
     * @param string $packageName
     * @param array $manifest
     * @return Reference
     */
    private function createVersionStrategy($packageName, array $manifest)
    {
        if ($manifest['enabled']) {
            return $this->createManifestVersionStrategy($packageName, $manifest);
        }

        $versionStrategy = new Reference($this->namespaceService('version_strategy.empty'));

        return $this->createAssetVersionStrategy($packageName, $versionStrategy);
    }

    /**
     * @param string $packageName
     * @param array $config
     * @return Reference
     */
    private function createManifestVersionStrategy($packageName, $config)
    {
        $loader = new DefinitionDecorator($this->namespaceService('manifest.loader.'.$config['format']));
        $loader
            ->addArgument($config['path'])
            ->addArgument($config['root_key'])
        ;

        $loaderId = $this->namespaceService("_package.$packageName.manifest_loader");
        $this->container->setDefinition($loaderId, $loader);

        $cachedLoader = new DefinitionDecorator($this->namespaceService('manifest.loader.cached'));
        $cachedLoader->addArgument(new Reference($loaderId));

        $cachedLoaderId = $this->namespaceService("_package.$packageName.manifest_loader_cached");
        $this->container->setDefinition($cachedLoaderId, $cachedLoader);

        $versionStrategy = new DefinitionDecorator($this->namespaceService('version_strategy.manifest'));
        $versionStrategy->addArgument(new Reference($cachedLoaderId));

        $versionStrategyId = $this->namespaceService("_package.$packageName.version_strategy");
        $this->container->setDefinition($versionStrategyId, $versionStrategy);

        return $this->createAssetVersionStrategy($packageName, new Reference($versionStrategyId));
    }

    /**
     * @param string $packageName
     * @param Reference $versionStrategy
     * @return Reference
     */
    private function createAssetVersionStrategy($packageName, $versionStrategy)
    {
        $version = new DefinitionDecorator($this->namespaceService('asset.version_strategy'));
        $version->addArgument($versionStrategy);

        $versionId = $this->namespaceService("_package.$packageName.version_strategy_asset");
        $this->container->setDefinition($versionId, $version);

        return new Reference($versionId);
    }

    /**
     * @param string $id
     * @return string
     */
    private function namespaceService($id)
    {
        return $this->alias.'.'.$id;
    }
}
