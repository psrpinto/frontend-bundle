<?php

namespace Rj\FrontendBundle\DependencyInjection;

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
        $loader->load('asset.yml');
        $loader->load('manifest.yml');

        if ($config['livereload']['enabled']) {
            $loader->load('livereload.yml');
            $container->getDefinition($this->getAlias().'.listener.livereload')
                ->addArgument($config['livereload']['url']);
        }

        // FIXME: There must be a better way to pass the configuration to the
        // compiler passes.
        $container->setParameter($this->getAlias().'.__config', $config);
    }
}
