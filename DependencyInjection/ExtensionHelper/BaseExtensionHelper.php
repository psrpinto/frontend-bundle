<?php

namespace Rj\FrontendBundle\DependencyInjection\ExtensionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

abstract class BaseExtensionHelper
{
    private $alias;
    protected $container;

    abstract public function createPathPackage($name, $config);
    abstract public function createUrlPackage($name, $config);

    public function __construct($alias, ContainerBuilder $container)
    {
        $this->alias = $alias;
        $this->container = $container;
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

    protected function createManifestLoader($packageName, $config)
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

        return new Reference($cachedLoaderId);
    }

    protected function namespaceService($id)
    {
        return $this->alias.'.'.$id;
    }

    private function matchPrefixes($prefixes, $flags = null)
    {
        $result = preg_grep("|^(https?:)?//|", $prefixes, $flags);

        return !empty($result);
    }
}
