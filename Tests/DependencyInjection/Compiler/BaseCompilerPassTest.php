<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class BaseCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    abstract protected function getCompilerPassInstance();

    public function setUp()
    {
        $this->container = new ContainerBuilder();
    }

    protected function process($config)
    {
        $this->container->setParameter('rj_frontend.__config', $config);

        $this->getCompilerPassInstance()->process($this->container);
    }

    protected function getConfig($manifest = false)
    {
        $packageConfig = array(
            'url_prefix' => null,
            'path_prefix' => 'foo',
            'manifest' => array(
                'enabled' => $manifest,
            ),
        );

        if ($manifest) {
            $packageConfig['manifest'] = array_merge($packageConfig['manifest'], array(
                'format' => 'json',
                'path' => 'foo',
                'root_key' => null,
            ));
        }

        return array('packages' => array('foo' => $packageConfig));
    }
}
