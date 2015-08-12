<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection;

use Rj\FrontendBundle\Util\Util;

class RjFrontendExtensionAssetTest extends RjFrontendExtensionBaseTest
{
    public function testPrefixPackageIsRegistered()
    {
        $this->load(array('packages' => array(
            'foo' => array(
                'prefixes' => 'foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.foo'));

        $this->assertEquals($package->getParent(), 'assets.path_package');
        $this->assertEquals($package->getArgument(0), 'foo');
        $this->assertEquals($package->getArgument(1), 'assets.empty_version_strategy');
    }

    public function testUrlPackageIsRegistered()
    {
        $this->load(array('packages' => array(
            'foo' => array(
                'prefixes' => 'http://foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.foo'));

        $this->assertEquals($package->getParent(), 'assets.url_package');
        $this->assertEquals($package->getArgument(0), array('http://foo'));
        $this->assertEquals($package->getArgument(1), 'assets.empty_version_strategy');
    }

    public function testPackageWithManifestIsRegistered()
    {
        $this->load(array('packages' => array(
            'foo' => array(
                'prefixes' => 'foo',
                'manifest' => array(
                    'path' => 'foo',
                ),
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.foo'));
        $this->assertEquals($package->getArgument(1), $this->namespaceService('_package.foo.version_strategy'));

        $version = $this->container->findDefinition($this->namespaceService('_package.foo.version_strategy'));
        $this->assertEquals($version->getParent(), $this->namespaceService('version_strategy.manifest'));
    }

    public function testPackageHasAliasTag()
    {
        $this->load(array('packages' => array(
            'foo' => array(
                'prefixes' => 'foo',
            ),
        )));

        $package = $this->container->findDefinition($this->namespaceService('_package.foo'));

        $this->assertTrue($package->hasTag($this->namespaceService('package.asset')));

        $tag = $package->getTag($this->namespaceService('package.asset'));
        $tag = $tag[0];
        $this->assertArrayHasKey('alias', $tag);
        $this->assertEquals('foo', $tag['alias']);
    }

    protected function setUp()
    {
        if (!Util::hasAssetComponent()) {
            return $this->markTestSkipped();
        }

        parent::setup();
    }
}
