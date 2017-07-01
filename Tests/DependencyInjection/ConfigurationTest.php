<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection;

use Rj\FrontendBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    public function testLivereloadIsEnabledByDefault()
    {
        $this->assertConfigurationEquals(
            [],
            [
                'livereload' => [
                    'enabled' => true,
                    'url' => '//localhost:35729/livereload.js',
                ],
            ],
            'livereload'
        );
    }

    public function testDefaultPackageFallbackPatterns()
    {
        $expected = [
            'fallback_patterns' => ['.*bundles\/.*'],
        ];

        $this->assertConfigurationEquals([], $expected, 'fallback_patterns');
    }

    public function testDefaultPackageDefaultPrefix()
    {
        $expected = ['prefix' => ['assets']];

        $this->assertConfigurationEquals([], $expected, 'prefix');
    }

    public function testDefaultPackageManifestIsDisabledByDefault()
    {
        $expected = $this->getManifestExpectedDefault();

        $this->assertConfigurationEquals([], $expected, 'manifest');
    }

    public function testDefaultPackageInferManifestPath()
    {
        $config = ['manifest' => true];

        $expected = $this->getManifestExpectedDefault();
        $expected['manifest']['enabled'] = true;
        $expected['manifest']['path'] = 'root_dir/../web/assets/manifest.json';

        $this->assertConfigurationEquals($config, $expected, 'manifest');
    }

    public function testDefaultPackageInferManifestPathWithPrefix()
    {
        $config = ['manifest' => true, 'prefix' => 'foo'];

        $expected = [
            'override_default_package' => true,
            'fallback_patterns' => ['.*bundles\/.*'],
            'livereload' => [
                'enabled' => true,
                'url' => '//localhost:35729/livereload.js',
            ],
            'prefix' => ['foo'],
            'packages' => [],
        ];

        $expected = array_merge($expected, $this->getManifestExpectedDefault());
        $expected['manifest']['enabled'] = true;
        $expected['manifest']['path'] = 'root_dir/../web/foo/manifest.json';

        $this->assertConfigurationEquals($config, $expected);
    }

    public function testPackagePrefixInvalidUrlAndPath()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = ['foo', 'http://foo'];

        $this->assertConfigurationInvalid($config, 'packages',
            '.*Packages cannot have both URL and path prefixes');
    }

    public function testPackagePrefixInvalidMultiplePaths()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = ['foo', 'bar'];

        $this->assertConfigurationInvalid($config, 'packages',
            '.*Packages can only have one path prefix');
    }

    public function testPackagePrefixString()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = 'foo';

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['prefix'] = ['foo'];

        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackagePrefixArray()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefix'] = ['http://foo', 'http://bar'];

        $expected = $this->getPackagesExpectedDefault();
        $expected['packages']['app']['prefix'] = ['http://foo', 'http://bar'];

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

    /**
     * @return array
     */
    private function getDefaultPackageConfig()
    {
        return [
            'packages' => [
                'app' => [
                    'prefix' => 'app_prefix',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    private function getManifestExpectedDefault()
    {
        return [
            'manifest' => [
                'enabled' => false,
                'format' => 'json',
                'root_key' => null,
            ],
        ];
    }

    /**
     * @return array
     */
    private function getPackagesExpectedDefault()
    {
        return [
            'packages' => [
                'app' => [
                    'prefix' => ['app_prefix'],
                    'manifest' => [
                        'enabled' => false,
                        'format' => 'json',
                        'root_key' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array       $config
     * @param string      $breadcrumbPath
     * @param null|string $expectedMessage
     */
    protected function assertConfigurationInvalid(array $config, $breadcrumbPath, $expectedMessage = null)
    {
        parent::assertPartialConfigurationIsInvalid(
            [$config],
            $breadcrumbPath,
            "/$expectedMessage/",
            $useRegExp = true
        );
    }

    /**
     * @param array       $config
     * @param null|string $breadcrumbPath
     */
    protected function assertConfigurationValid(array $config, $breadcrumbPath = null)
    {
        parent::assertConfigurationIsValid([$config], $breadcrumbPath);
    }

    /**
     * @param string      $config
     * @param array       $expected
     * @param null|string $breadcrumbPath
     */
    protected function assertConfigurationEquals($config, array $expected, $breadcrumbPath = null)
    {
        $this->assertProcessedConfigurationEquals([$config], $expected, $breadcrumbPath);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration('root_dir');
    }
}
