<?php

namespace Rj\FrontendBundle\Tests\Asset;

use Rj\FrontendBundle\Asset\ManifestVersionStrategy;
use Rj\FrontendBundle\Manifest\Loader\JsonManifestLoader;

class ManifestVersionStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyVersion()
    {
        $jsonFile = tempnam('/tmp', '');

        file_put_contents($jsonFile, json_encode(array(
            'foo.css' => 'foo-123.css',
        )));

        $vs = new ManifestVersionStrategy(new JsonManifestLoader($jsonFile));

        $this->assertEquals($vs->getVersion('foo.css'), '');

        $this->assertEquals($vs->applyVersion('foo.css'), 'foo-123.css');
        $this->assertEquals($vs->applyVersion('bar.css'), 'bar.css');

        unlink($jsonFile);
    }
}
