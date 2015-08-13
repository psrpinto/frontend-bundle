<?php

namespace Rj\FrontendBundle\DependencyInjection\ExtensionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

abstract class BaseExtensionHelper
{
    private $alias;
    protected $container;

    abstract protected function createPackage($name, $prefixes, $manifest, $isUrl = false);
    abstract protected function getPackageTag();

    public function __construct($alias, ContainerBuilder $container)
    {
        $this->alias = $alias;
        $this->container = $container;
    }

    public function createPathPackage($name, $config)
    {
        return $this->configurePackage(
            $name,
            $this->createPackage($name, $config['prefixes'], $config['manifest'])
        );
    }

    public function createUrlPackage($name, $config)
    {
        return $this->configurePackage(
            $name,
            $this->createPackage($name, $config['prefixes'], $config['manifest'], true)
        );
    }

    public function getPackageId($name)
    {
        return $this->namespaceService("_package.$name");
    }

    public function hasUrlPrefix($prefixes)
    {
        return $this->matchPrefixes($prefixes);
    }

    public function hasPathPrefix($prefixes)
    {
        return $this->matchPrefixes($prefixes, PREG_GREP_INVERT);
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

    private function configurePackage($packageName, $package)
    {
        return $package
            ->setPublic(false)
            ->addTag($this->getPackageTag(), array('alias' => $packageName))
        ;
    }

    private function matchPrefixes($prefixes, $flags = null)
    {
        $result = preg_grep("|^(https?:)?//|", $prefixes, $flags);

        return !empty($result);
    }
}
