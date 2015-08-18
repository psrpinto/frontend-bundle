<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection;

use Rj\FrontendBundle\Util\Util;

class RjFrontendExtensionTemplatingTest extends RjFrontendExtensionBaseTest
{
    public function testPathPackageIsRegistered()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefixes' => 'foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));

        $this->assertEquals($package->getParent(), $this->namespaceService('templating.package.path'));
        $this->assertEquals($package->getArgument(0), 'foo');
        $this->assertEquals($package->getArgument(1), $this->namespaceService('version_strategy.empty'));
    }

    public function testUrlPackageIsRegistered()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefixes' => 'http://foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));

        $this->assertEquals($package->getParent(), $this->namespaceService('templating.package.url'));
        $this->assertEquals($package->getArgument(0), array('http://foo'));
        $this->assertEquals($package->getArgument(1), $this->namespaceService('version_strategy.empty'));
    }

    public function testPackageWithManifestIsRegistered()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefixes' => 'foo',
                'manifest' => array(
                    'path' => 'foo',
                ),
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));
        $this->assertEquals($package->getArgument(0), 'foo');
        $this->assertEquals($package->getArgument(1), $this->namespaceService('_package.app.version_strategy'));
    }

    public function testPackageHasAliasTag()
    {
        $this->load(array('packages' => array(
            'app' => array(
                'prefixes' => 'foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.app'));

        $this->assertTrue($package->hasTag($this->namespaceService('package.templating')));

        $tag = $package->getTag($this->namespaceService('package.templating'));
        $tag = $tag[0];
        $this->assertArrayHasKey('alias', $tag);
        $this->assertEquals('app', $tag['alias']);
    }

    public function testFallbackPackageIsRegistered()
    {
        $this->load();

        $package = $this->container->findDefinition($this->namespaceService('package.fallback'));

        $this->assertEquals($package->getParent(), $this->namespaceService('templating.package.fallback'));
        $this->assertFalse($package->isPublic());
        $this->assertEquals($package->getArgument(0), array('.*bundles\/.*'));
    }

    protected function setUp()
    {
        if (Util::hasAssetComponent()) {
            return $this->markTestSkipped();
        }

        parent::setup();
    }
}
