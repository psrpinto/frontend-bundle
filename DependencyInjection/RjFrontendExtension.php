<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Rj\FrontendBundle\Util\Util;
use Rj\FrontendBundle\DependencyInjection\ExtensionHelper\AssetExtensionHelper;
use Rj\FrontendBundle\DependencyInjection\ExtensionHelper\TemplatingExtensionHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RjFrontendExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('version_strategy.yml');
        $loader->load('manifest.yml');
        $loader->load('package.yml');

        if ($config['livereload']['enabled']) {
            $loader->load('livereload.yml');
            $container->getDefinition($this->namespaceService('livereload.listener'))
                ->addArgument($config['livereload']['url']);
        }

        $helper = Util::hasAssetComponent()
            ? new AssetExtensionHelper($this->getAlias(), $container, $loader)
            : new TemplatingExtensionHelper($this->getAlias(), $container, $loader)
        ;

        $container->setDefinition(
            $this->namespaceService('package.fallback'),
            $helper->createFallbackPackage($config['fallback_patterns'])
        );

        foreach ($config['packages'] as $name => $packageConfig) {
            $prefixes = $packageConfig['prefixes'];
            $hasUrlPrefix = $helper->hasUrlPrefix($prefixes);
            $hasPathPrefix = $helper->hasPathPrefix($prefixes);

            if ($hasUrlPrefix && $hasPathPrefix) {
                throw new \LogicException("The '$name' package cannot have both URL and path prefixes");
            }

            if ($hasPathPrefix && count($prefixes) > 1) {
                throw new \LogicException("The '$name' package can only have one path prefix");
            }

            $package = $hasUrlPrefix
                ? $helper->createUrlPackage($name, $packageConfig)
                : $helper->createPathPackage($name, $packageConfig)
            ;

            $container->setDefinition($helper->getPackageId($name), $package);
        }
    }

    private function namespaceService($id)
    {
        return $this->getAlias().'.'.$id;
    }
}
