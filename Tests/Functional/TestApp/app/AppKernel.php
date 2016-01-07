<?php

namespace Rj\FrontendBundle\Tests\Functional\TestApp\app;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;

        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Rj\FrontendBundle\RjFrontendBundle(),
            new \Rj\FrontendBundle\Tests\Functional\TestApp\TestBundle\TestBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');

        foreach ($this->config as $key => $values) {
            $loader->load(function ($container) use ($key, $values) {
                $container->loadFromExtension($key, $values);
            });
        }
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/rj_frontend';
    }
}
