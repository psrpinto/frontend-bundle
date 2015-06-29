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
        $env = $container->getParameter('kernel.environment');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));

        if ('dev' === $env && $config['livereload']['enabled']) {
            $loader->load('livereload.yml');
            $container->getDefinition('rj_frontend.listener.livereload')
                ->addArgument($config['livereload']['url']);
        }
    }
    }
}
