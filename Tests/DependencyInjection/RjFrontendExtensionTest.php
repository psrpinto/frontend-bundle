<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection;

class RjFrontendExtensionTest extends RjFrontendExtensionBaseTest
{
    protected function setUp()
    {
        parent::setup();

        $this->container->setParameter('kernel.root_dir', 'root_dir');
    }

    public function testInjectLivereloadListenerIsRegistered()
    {
        $this->load([
            'livereload' => [
                'enabled' => true,
                'url' => 'foo',
            ],
        ]);

        $this->assertContainerBuilderHasService(
            $this->namespaceService('livereload.listener'),
            'Rj\FrontendBundle\EventListener\InjectLiveReloadListener'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            $this->namespaceService('livereload.listener'),
            $argumentIndex = 0,
            'foo'
        );
    }

    public function testInjectLivereloadListenerIsNotRegistered()
    {
        $this->load(['livereload' => false]);

        $this->assertContainerBuilderNotHasService($this->namespaceService('livereload.listener'));
    }

    public function testPackageIsRegisteredAndPrivate()
    {
        $this->load(['packages' => [
            'foo' => [
                'prefix' => 'foo_prefix',
            ],
        ]]);

        $this->assertContainerBuilderHasService($this->namespaceService('_package.foo'));
        $this->assertFalse($this->container->findDefinition($this->namespaceService('_package.foo'))->isPublic());
    }
}
