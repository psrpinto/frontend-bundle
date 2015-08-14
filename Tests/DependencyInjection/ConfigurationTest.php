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
                    'url' => '/livereload.js?port=37529',
                ),
            ),
            'livereload'
        );
    }

    public function testPackageManifestIsDisabledByDefault()
    {
        $config = $this->getDefaultPackageConfig();

        $expected = $this->getDefaultPackageExpected();
        $expected['packages']['app']['manifest']['enabled'] = false;
        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackagePrefixString()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefixes'] = 'foo';

        $expected = $this->getDefaultPackageExpected();
        $expected['packages']['app']['prefixes'] = array('foo');

        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    public function testPackagePrefixArray()
    {
        $config = $this->getDefaultPackageConfig();
        $config['packages']['app']['prefixes'] = array('foo', 'bar');

        $expected = $this->getDefaultPackageExpected();
        $expected['packages']['app']['prefixes'] = array('foo', 'bar');

        $this->assertConfigurationEquals($config, $expected, 'packages');
    }

    private function getDefaultPackageConfig()
    {
        return array(
            'packages' => array(
                'app' => array(),
            ),
        );
    }

    private function getDefaultPackageExpected()
    {
        return array(
            'packages' => array(
                'app' => array(
                    'prefixes' => array(null),
                    'manifest' => array(
                        'enabled' => false,
                        'format' => 'json',
                        'root_key' => null,
                    ),
                ),
            ),
        );
    }

    protected function assertConfigurationEquals($config, $expected, $breadcrumbPath = null)
    {
        $this->assertProcessedConfigurationEquals(array($config), $expected, $breadcrumbPath);
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
