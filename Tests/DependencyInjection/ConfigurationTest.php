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
                'packages' => array(),
            )
        );
    }

    public function testPackagePrefixString()
    {
        $this->assertConfigurationEquals(
            array(
                'packages' => array(
                    'app' => array(
                        'prefixes' => 'foo',
                    ),
                ),
            ),
            array(
                'livereload' => array(
                    'enabled' => false,
                    'url' => '/livereload.js?port=37529',
                ),
                'packages' => array(
                    'app' => array(
                        'prefixes' => array('foo'),
                        'manifest' => array(
                            'enabled' => false,
                            'format' => 'json',
                            'root_key' => null,
                        ),
                    ),
                ),
            )
        );
    }

    protected function assertConfigurationEquals($config, $expected)
    {
        $this->assertProcessedConfigurationEquals(array($config), $expected);
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
