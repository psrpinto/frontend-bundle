<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection;

use Rj\FrontendBundle\Util\Util;

class RjFrontendExtensionAssetTest extends RjFrontendExtensionBaseTest
{
    public function testPathPackageIsRegistered()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefix' => 'foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));

        $this->assertEquals($package->getParent(), $this->namespaceService('asset.package.path'));
        $this->assertFalse($package->isPublic());
        $this->assertEquals($package->getArgument(0), 'foo');
        $this->assertEquals($package->getArgument(1), $this->namespaceService('_package.app.version_strategy_asset'));

        $vs = $this->container->findDefinition($this->namespaceService('_package.app.version_strategy_asset'));
        $this->assertEquals($vs->getArgument(0), $this->namespaceService('version_strategy.empty'));
    }

    public function testUrlPackageIsRegistered()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefix' => 'http://foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));

        $this->assertEquals($package->getParent(), $this->namespaceService('asset.package.url'));
        $this->assertFalse($package->isPublic());
        $this->assertEquals($package->getArgument(0), array('http://foo'));
        $this->assertEquals($package->getArgument(1), $this->namespaceService('_package.app.version_strategy_asset'));

        $vs = $this->container->findDefinition($this->namespaceService('_package.app.version_strategy_asset'));
        $this->assertEquals($vs->getArgument(0), $this->namespaceService('version_strategy.empty'));
    }

    public function testPackageWithManifestIsRegistered()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefix' => 'foo',
                'manifest' => array(
                    'path' => 'foo',
                ),
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));
        $this->assertEquals($package->getArgument(1), $this->namespaceService('_package.app.version_strategy_asset'));

        $vsAsset = $this->container->findDefinition($this->namespaceService('_package.app.version_strategy_asset'));
        $vs = $this->container->findDefinition($vsAsset->getArgument(0));
        $this->assertEquals($vs->getParent(), $this->namespaceService('version_strategy.manifest'));
    }

    public function testPackageHasAliasTag()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefix' => 'foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));

        $this->assertTrue($package->hasTag($this->namespaceService('package.asset')));

        $tag = $package->getTag($this->namespaceService('package.asset'));
        $tag = $tag[0];
        $this->assertArrayHasKey('alias', $tag);
        $this->assertEquals('app', $tag['alias']);
    }

    public function testFallbackPackageIsRegistered()
    {
        $this->load();

        $package = $this->container->findDefinition($this->namespaceService('package.fallback'));

        $this->assertEquals($package->getParent(), $this->namespaceService('asset.package.fallback'));
        $this->assertFalse($package->isPublic());
        $this->assertEquals($package->getArgument(0), array('.*bundles\/.*'));
    }

    protected function setUp()
    {
        if (!Util::hasAssetComponent()) {
            return $this->markTestSkipped();
        }

        parent::setup();

        $this->container->setParameter('kernel.root_dir', 'root_dir');
    }
}
