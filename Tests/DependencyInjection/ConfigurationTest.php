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

    protected function assertConfigurationEquals($config, $expected)
    {
        $this->assertProcessedConfigurationEquals(array($config), $expected);
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
