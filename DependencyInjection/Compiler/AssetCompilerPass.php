<?php

namespace Rj\FrontendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class AssetCompilerPass extends BaseCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $config = $this->getConfig($container);

        foreach ($config['packages'] as $packageName => $config) {
            $packageId = 'assets._package_'.$packageName;

            if ($container->hasDefinition($packageId)) {
                throw new \LogicException("A package named '$packageName' has already been defined");
            }

            if ($config['manifest']['enabled']) {
                $version = $this->createManifestVersionStrategy($container, $packageName, $config['manifest']);
            } else {
                $version = new Reference('assets.empty_version_strategy');
            }

            $container->setDefinition($packageId, $this->createPackageDefinition($packageName, $config, $version));

            $container->getDefinition('assets.packages')
                ->addMethodCall('addPackage', array($packageName, new Reference($packageId)))
            ;
        }
    }

    private function createManifestVersionStrategy($container, $packageName, $config)
    {
        $loader = new DefinitionDecorator($this->getAlias().'.manifest_loader.'.$config['format']);
        $loader
            ->addArgument($config['path'])
            ->addArgument($config['root_key'])
        ;

        $loaderId = $this->getAlias().'._manifest_loader_'.$packageName;
        $container->setDefinition($loaderId, $loader);

        $cachedLoader = new DefinitionDecorator($this->getAlias().'.manifest_loader.cached');
        $cachedLoader->addArgument(new Reference($loaderId));

        $cachedLoaderId = $this->getAlias().'._manifest_loader_cached_'.$packageName;
        $container->setDefinition($cachedLoaderId, $cachedLoader);

        $version = new DefinitionDecorator($this->getAlias().'.version_strategy.manifest');
        $version->addArgument(new Reference($cachedLoaderId));

        $versionId = 'assets._version_manifest_'.$packageName;
        $container->setDefinition($versionId, $version);

        return new Reference($versionId);
    }

    private function createPackageDefinition($name, $config, $version)
    {
        $pathPrefix  = isset($config['path_prefix']) ? $config['path_prefix'] : null;
        $urlPrefixes = $config['url_prefix'];

        if ($pathPrefix && !empty($urlPrefixes)) {
            throw new \LogicException("A package cannot have both a 'path_prefix' and a 'url_prefix'");
        }

        if (empty($urlPrefixes)) {
            $package = new DefinitionDecorator('assets.path_package');

            return $package
                ->setPublic(false)
                ->replaceArgument(0, $pathPrefix)
                ->replaceArgument(1, $version)
            ;
        }

        $package = new DefinitionDecorator('assets.url_package');

        return $package
            ->setPublic(false)
            ->replaceArgument(0, $urlPrefixes)
            ->replaceArgument(1, $version)
        ;
    }
}
