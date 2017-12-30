<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Rj\FrontendBundle\Util\Util;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
     * @param string           $alias
     * @param ContainerBuilder $container
     */
    public function __construct($alias, ContainerBuilder $container)
    {
        $this->alias = $alias;
        $this->container = $container;
    }

    /**
     * @param array           $config
     * @param LoaderInterface $loader
     */
    public function load(array $config, LoaderInterface $loader)
    {
        $loader->load('asset.yml');

        if ($config['override_default_package']) {
            $loader->load('fallback.yml');

            $defaultPackage = $this->createPackage('default', [
                'prefix' => $config['prefix'],
                'manifest' => $config['manifest'],
            ]);

            $defaultPackageId = $this->getPackageId('default');
            $this->container->setDefinition($defaultPackageId, $defaultPackage);

            $this->container->getDefinition($this->namespaceService('package.fallback'))
                ->addArgument($config['fallback_patterns'])
                ->addArgument(new Reference($defaultPackageId));
        }

        foreach ($config['packages'] as $name => $packageConfig) {
            $packageTag = $this->namespaceService('package.asset');
            $package = $this->createPackage($name, $packageConfig)
                ->addTag($packageTag, ['alias' => $name]);

            $this->container->setDefinition($this->getPackageId($name), $package);
        }
    }

    /**
     * @param string $name
     * @param array  $config
     *
     * @return Definition
     */
    private function createPackage($name, array $config)
    {
        $prefixes = $config['prefix'];
        $isUrl = Util::containsUrl($prefixes);

        $packageDefinition = $isUrl
            ? new ChildDefinition($this->namespaceService('asset.package.url'))
            : new ChildDefinition($this->namespaceService('asset.package.path'))
        ;

        if ($config['manifest']['enabled']) {
            $versionStrategy = $this->createManifestVersionStrategy($name, $config['manifest']);
        } else {
            $versionStrategy = new Reference($this->namespaceService('version_strategy.empty'));
        }

        return $packageDefinition
            ->addArgument($isUrl ? $prefixes : $prefixes[0])
            ->addArgument($versionStrategy)
            ->setPublic(false);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getPackageId($name)
    {
        return $this->namespaceService("_package.$name");
    }

    /**
     * @param string $packageName
     * @param array  $config
     *
     * @return Reference
     */
    private function createManifestVersionStrategy($packageName, $config)
    {
        $loader = new ChildDefinition($this->namespaceService('manifest.loader.'.$config['format']));
        $loader
            ->addArgument($config['path'])
            ->addArgument($config['root_key'])
        ;

        $loaderId = $this->namespaceService("_package.$packageName.manifest_loader");
        $this->container->setDefinition($loaderId, $loader);

        $cachedLoader = new ChildDefinition($this->namespaceService('manifest.loader.cached'));
        $cachedLoader->addArgument(new Reference($loaderId));

        $cachedLoaderId = $this->namespaceService("_package.$packageName.manifest_loader_cached");
        $this->container->setDefinition($cachedLoaderId, $cachedLoader);

        $versionStrategy = new ChildDefinition($this->namespaceService('version_strategy.manifest'));
        $versionStrategy->addArgument(new Reference($cachedLoaderId));

        $versionStrategyId = $this->namespaceService("_package.$packageName.version_strategy");
        $this->container->setDefinition($versionStrategyId, $versionStrategy);

        return new Reference($versionStrategyId);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function namespaceService($id)
    {
        return $this->alias.'.'.$id;
    }
}
