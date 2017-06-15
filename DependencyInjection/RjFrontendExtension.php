<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RjFrontendExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration(array(), $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('console.yml');
        $loader->load('version_strategy.yml');
        $loader->load('manifest.yml');

        if ($config['livereload']['enabled']) {
            $loader->load('livereload.yml');
            $container->getDefinition($this->id('livereload.listener'))
                ->addArgument($config['livereload']['url']);
        }

        $helper = new AssetExtensionHelper($this->getAlias(), $container, $loader);

        if ($config['override_default_package']) {
            $loader->load('fallback.yml');

            $defaultPackage = $helper->createPackage('default', array(
                'prefix'   => $config['prefix'],
                'manifest' => $config['manifest'],
            ));

            $defaultPackageId = $helper->getPackageId('default');
            $container->setDefinition($defaultPackageId, $defaultPackage);

            $fallbackPackage = $helper->createFallbackPackage(
                $config['fallback_patterns'],
                new Reference($defaultPackageId)
            );

            $container->setDefinition($this->id('package.fallback'), $fallbackPackage);
        }

        foreach ($config['packages'] as $name => $packageConfig) {
            $package = $helper->createPackage($name, $packageConfig)
                ->addTag($helper->getPackageTag(), array('alias' => $name))
            ;

            $container->setDefinition($helper->getPackageId($name), $package);
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('kernel.root_dir'));
    }

    private function id($id)
    {
        return $this->getAlias().'.'.$id;
    }
}
