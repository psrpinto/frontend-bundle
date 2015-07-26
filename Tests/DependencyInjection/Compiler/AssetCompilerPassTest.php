<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection\Compiler;

use Rj\FrontendBundle\Util\Util;
use Rj\FrontendBundle\DependencyInjection\Compiler\AssetCompilerPass;

class AssetCompilerPassTest extends BaseCompilerPassTest
{
    public function testCreatesPackage()
    {
        $this->process($this->getConfig());

        $this->assertTrue($this->container->hasDefinition('assets._package_foo'));
        $this->assertFalse($this->container->getDefinition('assets._package_foo')->isPublic());
    }

    public function testPackageIsRegisteredIntoAssets()
    {
        $this->process($this->getConfig());

        $packages = $this->container->getDefinition('assets.packages');
        $methodCalls = $packages->getMethodCalls();

        $this->assertTrue($packages->hasMethodCall('addPackage'));
        $this->assertEquals(1, count($methodCalls));

        $addPackageArguments = $methodCalls[0][1];
        $this->assertEquals('foo', $addPackageArguments[0]);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $addPackageArguments[1]);

        $package = new \ReflectionObject($packageService = $addPackageArguments[1]);
        $id = $package->getProperty('id');
        $id->setAccessible(true);
        $this->assertEquals('assets._package_foo', $id->getValue($packageService));
    }

    public function testVersionStrategyWithNoManifest()
    {
        $this->process($this->getConfig());

        $package = $this->container->getDefinition('assets._package_foo');
        $this->assertEquals('assets.empty_version_strategy', sprintf('%s', $package->getArgument(1)));
    }

    public function testVersionStrategyWithManifest()
    {
        $this->process($this->getConfig($manifest = true));

        $package = $this->container->getDefinition('assets._package_foo');
        $this->assertEquals('assets._version_manifest_foo', sprintf('%s', $package->getArgument(1)));
    }

    public function setUp()
    {
        if (!Util::hasAssetComponent()) {
            return $this->markTestSkipped();
        }

        parent::setUp();

        $this->container
            ->register('assets.packages')
            ->setPublic(false)
        ;
    }

    protected function getCompilerPassInstance()
    {
        return new AssetCompilerPass();
    }
}
