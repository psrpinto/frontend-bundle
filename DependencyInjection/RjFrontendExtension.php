<?php

namespace Rj\FrontendBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Kernel;

class RjFrontendExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration([], $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('console.yml');
        $loader->load('version_strategy.yml');
        $loader->load('manifest.yml');

        if (version_compare(Kernel::VERSION, '3.3.0', '>=')) {
            $loader->load('commands.yml');
        }

        if ($config['livereload']['enabled']) {
            $loader->load('livereload.yml');
            $container->getDefinition($this->namespaceService('livereload.listener'))
                ->addArgument($config['livereload']['url']);
        }

        $assetExtensionLoader = new AssetExtensionLoader($this->getAlias(), $container);
        $assetExtensionLoader->load($config, $loader);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('kernel.root_dir'));
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function namespaceService($id)
    {
        return $this->getAlias().'.'.$id;
    }
}
