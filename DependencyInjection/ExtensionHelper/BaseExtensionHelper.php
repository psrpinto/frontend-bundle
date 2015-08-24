<?php

namespace Rj\FrontendBundle\DependencyInjection\ExtensionHelper;

use Rj\FrontendBundle\Util\Util;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

abstract class BaseExtensionHelper
{
    private $alias;
    protected $container;

    abstract public function getPackageTag();
    abstract protected function getPackageDefinition($isUrl);
    abstract protected function getFallbackPackageDefinition();

    public function __construct($alias, ContainerBuilder $container)
    {
        $this->alias = $alias;
        $this->container = $container;
    }

    public function createPackage($name, $config)
    {
        $prefixes = $config['prefix'];
        $isUrl = Util::containsUrl($prefixes);

        return $this->getPackageDefinition($isUrl)
            ->addArgument($isUrl ? $prefixes : $prefixes[0])
            ->addArgument($this->createVersionStrategy($name, $config['manifest']))
            ->setPublic(false)
        ;
    }

    public function createFallbackPackage($patterns, $customDefaultPackage)
    {
        return $this->getFallbackPackageDefinition()
            ->setPublic(false)
            ->addArgument($patterns)
            ->addArgument($customDefaultPackage)
        ;
    }

    public function getPackageId($name)
    {
        return $this->namespaceService("_package.$name");
    }

    protected function createVersionStrategy($packageName, $manifest)
    {
        if ($manifest['enabled']) {
            return $this->createManifestVersionStrategy($packageName, $manifest);
        }

        return $this->createEmptyVersionStrategy($packageName);
    }

    protected function createEmptyVersionStrategy($packageName)
    {
        return new Reference($this->namespaceService('version_strategy.empty'));
    }

    protected function createManifestVersionStrategy($packageName, $config)
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

        return new Reference($versionStrategyId);
    }

    protected function namespaceService($id)
    {
        return $this->alias.'.'.$id;
    }
}
