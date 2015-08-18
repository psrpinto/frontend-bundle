<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection\Compiler\Packages;

use Rj\FrontendBundle\Tests\DependencyInjection\Compiler\BaseCompilerPassTest;
use Rj\FrontendBundle\Util\Util;
use Rj\FrontendBundle\DependencyInjection\Compiler\Packages\TemplatingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TemplatingCompilerPassTest extends BaseCompilerPassTest
{
    /**
     * @expectedException        LogicException
     * @expectedExceptionMessage The 'assets' templating helper is not registered in the container
     */
    public function testThrowsExceptionIfAssetsHelperIsNotRegistered()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.templating'), array('alias' => 'foo'));
        $this->setDefinition('foo_service', $package);

        $this->container->removeDefinition('templating.helper.assets');

        $this->compile();
    }

    /**
     * @expectedException        LogicException
     * @expectedExceptionMessage The tag for the service with id 'foo_service' must define an 'alias' attribute
     */
    public function testThrowsExceptionIfPackageWithNoTag()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.templating'));
        $this->setDefinition('foo_service', $package);

        $this->compile();
    }

    /**
     * @expectedException        LogicException
     * @expectedExceptionMessage A package named 'foo' has already been registered
     */
    public function testThrowsExceptionIfPackageIsAlreadyRegisteredWithAssetsHelper()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.templating'), array('alias' => 'foo'));
        $this->setDefinition('foo_service', $package);

        $this->container->removeDefinition('templating.helper.assets');

        $this->registerPackagesService(array(
            new Reference('default_package'),
            array('foo', new Reference('foo_package')),
        ));

        $this->compile();
    }

    /**
     * @expectedException        LogicException
     * @expectedExceptionMessage Multiple packages were found with alias 'foo'. Package alias' must be unique
     */
    public function testThrowsExceptionIfDuplicatePackage()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.templating'), array('alias' => 'foo'));
        $this->setDefinition('foo_service', $package);

        $package2 = new Definition();
        $package2->addTag($this->namespaceService('package.templating'), array('alias' => 'foo'));
        $this->setDefinition('foo_service_2', $package2);

        $this->compile();
    }

    public function testPackageIsRegisteredIntoAssetsHelper()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.templating'), array('alias' => 'foo'));
        $this->setDefinition('foo_service', $package);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'templating.helper.assets',
            'addPackage',
            array('foo', new Reference('foo_service'))
        );
    }

    public function testDefaultPackageIsRegisteredIntoAssets()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.templating'), array('alias' => 'default'));
        $this->setDefinition('default_service', $package);

        $this->container->removeDefinition('templating.helper.assets');
        $this->registerPackagesService(array(
            new Reference('default_package'),
            array('foo', new Reference('foo_package')),
        ));

        $this
            ->registerService($this->namespaceService('package.fallback'), null)
            ->addArgument(array('foo_pattern'))
            ->setPublic(false)
        ;

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            $this->namespaceService('package.fallback'),
            0,
            array('foo_pattern')
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $this->namespaceService('package.fallback'),
            'setPackage',
            array(new Reference('default_service'))
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $this->namespaceService('package.fallback'),
            'setFallback',
            array(new Reference('default_package'))
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'templating.helper.assets',
            0,
            new Reference($this->namespaceService('package.fallback'))
        );
    }

    public function setUp()
    {
        if (Util::hasAssetComponent()) {
            return $this->markTestSkipped();
        }

        parent::setUp();

        $this->registerPackagesService();
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TemplatingCompilerPass());
    }

    private function registerPackagesService($arguments = array())
    {
        $service = $this
            ->registerService('templating.helper.assets', null)
            ->setPublic(false)
        ;

        foreach ($arguments as $argument) {
            $service->addArgument($argument);
        }
    }
}
