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

    protected function createPackage($name, $prefixes, $manifest, $isUrl = false)
    {
        $package = $isUrl
            ? new DefinitionDecorator($this->namespaceService('asset.package.url'))
            : new DefinitionDecorator($this->namespaceService('asset.package.path'))
        ;

        return $package
            ->addArgument($isUrl ? $prefixes : $prefixes[0])
            ->addArgument($this->createVersionStrategy($name, $manifest))
        ;
    }

    protected function getPackageTag()
    {
        return $this->namespaceService('package.asset');
    }

    protected function createEmptyVersionStrategy($packageName)
    {
        $versionStrategy = parent::createEmptyVersionStrategy($packageName);

        return $this->createAssetVersionStrategy($packageName, $versionStrategy);
    }

    protected function createManifestVersionStrategy($packageName, $config)
    {
        $versionStrategy = parent::createManifestVersionStrategy($packageName, $config);

        return $this->createAssetVersionStrategy($packageName, $versionStrategy);
    }

    private function createAssetVersionStrategy($packageName, $versionStrategy)
    {
        $version = new DefinitionDecorator($this->namespaceService('asset.version_strategy'));
        $version->addArgument($versionStrategy);

        $versionId = $this->namespaceService("_package.$packageName.version_strategy_asset");
        $this->container->setDefinition($versionId, $version);

        return new Reference($versionId);
    }
}
