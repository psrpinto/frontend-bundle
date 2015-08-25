<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection;

use Rj\FrontendBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    public function testLivereloadIsDisabledByDefault()
    {
        $this->assertConfigurationEquals(
            array(),
            array(
                'livereload' => array(
                    'enabled' => false,
                    'url' => '//localhost:35729/livereload.js',
                ),
            ),
            'livereload'
        );
    }

    public function testDefaultPackageFallbackPatterns()
    {
        $expected = array(
            'fallback_patterns' => array('.*bundles\/.*'),
        );

        $this->assertConfigurationEquals(array(), $expected, 'fallback_patterns');
    }

    public function testDefaultPackageDefaultPrefix()
    {
        $expected = array('prefix' => array('assets'));

        $this->assertConfigurationEquals(array(), $expected, 'prefix');
    }

    public function testDefaultPackageManifestIsDisabledByDefault()
    {
        $expected = $this->getManifestExpectedDefault();

        $this->assertConfigurationEquals(array(), $expected, 'manifest');
    }

    public function testDefaultPackageInferManifestPath()
    {
        $config = array('manifest' => true);

        $expected = $this->getManifestExpectedDefault();
        $expected['manifest']['enabled'] = true;
        $expected['manifest']['path'] = 'root_dir/../web/assets/manifest.json';

        $this->assertConfigurationEquals($config, $expected, 'manifest');
    }

    public function testDefaultPackageInferManifestPathWithPrefix()
    {
        $config = array('manifest' => true, 'prefix' => 'foo');

        $expected = array(
            'override_default_package' => true,
            'fallback_patterns' => array('.*bundles\/.*'),
            'livereload' => array(
                'enabled' => false,
                'url' => '//localhost:35729/livereload.js',
            ),
            'prefix' => array('foo'),
            'packages' => array(),
        );

        $expected = array_merge($expected, $this->getManifestExpectedDefault());
        $expected['manifest']['enabled'] = true;
        $expected['manifest']['path'] = 'root_dir/../web/foo/manifest.json';

        $this->assertConfigurationEquals($config, $expected);
    }

    public function testPackagePrefixInvalidUrlAndPath()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = array('foo', 'http://foo');

        $this->assertConfigurationInvalid($config, 'packages',
            '.*Packages cannot have both URL and path prefixes');
    }

    public function testPackagePrefixInvalidMultiplePaths()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = array('foo', 'bar');

        $this->assertConfigurationInvalid($config, 'packages',
            '.*Packages can only have one path prefix');
    }

    public function testPackagePrefixString()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = 'foo';

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['prefix'] = array('foo');

        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackagePrefixArray()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = array('http://foo', 'http://bar');

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['prefix'] = array('http://foo', 'http://bar');

        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackageManifestIsDisabledByDefault()
    {
        $config = $this->getDefaultPackageConfig();

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['manifest']['enabled'] = false;
        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackageManifestUnsupportedFormat()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['manifest']['path'] = 'foo.json';
        $config['packages']['app']['manifest']['format'] = 'yaml';

        $this->assertConfigurationInvalid($config, 'packages',
            '.*For the moment only JSON manifest files are supported');
    }

    public function testPackageManifestEnabled()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['manifest']['enabled'] = true;
        $config['packages']['app']['manifest']['path'] = 'foo.json';

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['manifest']['enabled'] = true;
        $expected['packages']['app']['manifest']['path'] = 'foo.json';
        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackageManifestString()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['manifest'] = 'foo.json';

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['manifest']['enabled'] = true;
        $expected['packages']['app']['manifest']['path'] = 'foo.json';
        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackageInferManifestPath()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['manifest'] = true;

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['manifest']['enabled'] = true;
        $expected['packages']['app']['manifest']['path'] = 'root_dir/../web/app_prefix/manifest.json';
        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    private function getDefaultPackageConfig()
    {
        return array(
            'packages' => array(
                'app' => array(
                    'prefix' => 'app_prefix',
                ),
            ),
        );
    }

    private function getManifestExpectedDefault()
    {
        return array(
            'manifest' => array(
                'enabled' => false,
                'format' => 'json',
                'root_key' => null,
            ),
        );
    }

    private function getPackagesExpectedDefault()
    {
        return array(
            'packages' => array(
                'app' => array(
                    'prefix' => array('app_prefix'),
                    'manifest' => array(
                        'enabled' => false,
                        'format' => 'json',
                        'root_key' => null,
                    ),
                ),
            ),
        );
    }

    protected function assertConfigurationInvalid(array $config, $breadcrumbPath, $expectedMessage = null)
    {
        parent::assertPartialConfigurationIsInvalid(
            array($config),
            $breadcrumbPath,
            "/$expectedMessage/",
            $useRegExp = true
        );
    }

    protected function assertConfigurationValid(array $config, $breadcrumbPath = null)
    {
        parent::assertConfigurationIsValid(array($config), $breadcrumbPath);
    }

    protected function assertConfigurationEquals($config, $expected, $breadcrumbPath = null)
    {
        $this->assertProcessedConfigurationEquals(array($config), $expected, $breadcrumbPath);
    }

    protected function getConfiguration()
    {
        return new Configuration('root_dir');
    }
}
