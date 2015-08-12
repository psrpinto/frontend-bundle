<?php

namespace Rj\FrontendBundle\DependencyInjection\ExtensionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class AssetExtensionHelper extends BaseExtensionHelper
{
    public function __construct($alias, ContainerBuilder $container, $loader)
    {
        parent::__construct($alias, $container);

        $loader->load('asset.yml');
    }

    public function createPathPackage($name, $config)
    {
        return $this->createPackage($name, $config['prefixes'][0], $config['manifest']);
    }

    public function createUrlPackage($name, $config)
    {
        return $this->createPackage($name, $config['prefixes'], $config['manifest'], true);
    }

    private function createPackage($name, $prefix, $manifest, $isUrl = false)
    {
        $package = $isUrl
            ? new DefinitionDecorator('assets.url_package')
            : new DefinitionDecorator('assets.path_package')
        ;

        $package->replaceArgument(0, $prefix);

        if ($manifest['enabled']) {
            $version = $this->createManifestVersionStrategy($name, $manifest);
        } else {
            $version = new Reference('assets.empty_version_strategy');
        }

        return $package
            ->setPublic(false)
            ->replaceArgument(1, $version)
            ->addTag($this->namespaceService('package.asset'), array('alias' => $name))
        ;
    }

    private function createManifestVersionStrategy($packageName, $config)
    {
        $loader = $this->createManifestLoader($packageName, $config);

        $version = new DefinitionDecorator($this->namespaceService('version_strategy.manifest'));
        $version->addArgument($loader);

        $versionId = $this->namespaceService("_package.$packageName.version_strategy");
        $this->container->setDefinition($versionId, $version);

        return new Reference($versionId);
    }
}
