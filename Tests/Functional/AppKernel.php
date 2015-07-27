<?php

namespace Rj\FrontendBundle\Tests\Functional;

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
            new \Rj\FrontendBundle\Tests\Functional\TestBundle\TestBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__."/config/config.yml");

        $config = $this->config;
        $loader->load(function ($container) use ($config) {
            $container->loadFromExtension('rj_frontend', $config);
        });
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/rj_frontend';
    }
}
