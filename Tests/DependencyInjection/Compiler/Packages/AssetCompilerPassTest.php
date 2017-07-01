<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection\Compiler\Packages;

use Rj\FrontendBundle\Tests\DependencyInjection\Compiler\BaseCompilerPassTest;
use Rj\FrontendBundle\DependencyInjection\Compiler\AssetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AssetCompilerPassTest extends BaseCompilerPassTest
{
    public function setUp()
    {
        parent::setUp();

        $this->registerPackagesService();
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage The Asset component is not registered in the container
     */
    public function testThrowsExceptionIfAssetComponentIsNotRegistered()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.asset'), ['alias' => 'foo']);
        $this->setDefinition('foo_service', $package);

        $this->container->removeDefinition('assets.packages');

        $this->compile();
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage The tag for the service with id 'foo_service' must define an 'alias' attribute
     */
    public function testThrowsExceptionIfPackageWithNoTag()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.asset'));
        $this->setDefinition('foo_service', $package);

        $this->compile();
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage A package named 'foo' has already been registered
     */
    public function testThrowsExceptionIfPackageIsAlreadyRegisteredWithAssetComponent()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.asset'), ['alias' => 'foo']);
        $this->setDefinition('foo_service', $package);

        $this->container->removeDefinition('assets.packages');

        $this->registerPackagesService([
            new Reference('default_package'),
            ['foo', new Reference('foo_package')],
        ]);

        $this->compile();
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Multiple packages were found with alias 'foo'. Package alias' must be unique
     */
    public function testThrowsExceptionIfDuplicatePackage()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.asset'), ['alias' => 'foo']);
        $this->setDefinition('foo_service', $package);

        $package2 = new Definition();
        $package2->addTag($this->namespaceService('package.asset'), ['alias' => 'foo']);
        $this->setDefinition('foo_service_2', $package2);

        $this->compile();
    }

    public function testPackageIsRegisteredIntoAssets()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.asset'), ['alias' => 'foo']);
        $this->setDefinition('foo_service', $package);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'assets.packages',
            'addPackage',
            ['foo', new Reference('foo_service')]
        );
    }

    public function testDefaultPackageIsRegisteredIntoAssets()
    {
        $package = new Definition();
        $package->addTag($this->namespaceService('package.asset'), ['alias' => 'default']);
        $this->setDefinition('default_service', $package);

        $this->container->removeDefinition('assets.packages');
        $this->registerPackagesService([
            new Reference('default_package'),
            ['foo', new Reference('foo_package')],
        ]);

        $this
            ->registerService($this->namespaceService('package.fallback'), null)
            ->addArgument(['foo_pattern'])
            ->setPublic(false)
        ;

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            $this->namespaceService('package.fallback'),
            0,
            ['foo_pattern']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $this->namespaceService('package.fallback'),
            'setFallback',
            [new Reference('default_package')]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'assets.packages',
            0,
            new Reference($this->namespaceService('package.fallback'))
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AssetCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    private function registerPackagesService($arguments = [])
    {
        $service = $this
            ->registerService('assets.packages', null)
            ->setPublic(false)
        ;

        foreach ($arguments as $argument) {
            $service->addArgument($argument);
        }
    }
}
