<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Rj\FrontendBundle\Util\Util;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class AssetExtensionHelper
{
    private $alias;
    private $container;

    public function __construct($alias, ContainerBuilder $container)
    {
        $this->alias = $alias;
        $this->container = $container;
    }

    public function createPackage($name, $config)
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

    public function createFallbackPackage($patterns, $customDefaultPackage)
    {
        $packageDefinition = new DefinitionDecorator($this->namespaceService('asset.package.fallback'));

        return $packageDefinition
            ->setPublic(false)
            ->addArgument($patterns)
            ->addArgument($customDefaultPackage);
    }

    public function getPackageId($name)
    {
        return $this->namespaceService("_package.$name");
    }

    private function createVersionStrategy($packageName, $manifest)
    {
        if ($manifest['enabled']) {
            return $this->createManifestVersionStrategy($packageName, $manifest);
        }

        $versionStrategy = new Reference($this->namespaceService('version_strategy.empty'));;

        return $this->createAssetVersionStrategy($packageName, $versionStrategy);
    }

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

    private function createAssetVersionStrategy($packageName, $versionStrategy)
    {
        $version = new DefinitionDecorator($this->namespaceService('asset.version_strategy'));
        $version->addArgument($versionStrategy);

        $versionId = $this->namespaceService("_package.$packageName.version_strategy_asset");
        $this->container->setDefinition($versionId, $version);

        return new Reference($versionId);
    }

    private function namespaceService($id)
    {
        return $this->alias.'.'.$id;
    }
}
