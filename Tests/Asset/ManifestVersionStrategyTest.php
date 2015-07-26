<?php

namespace Rj\FrontendBundle\Tests\Asset;

use Rj\FrontendBundle\Util\Util;
use Rj\FrontendBundle\Manifest\Loader\JsonManifestLoader;

class ManifestVersionStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyVersion()
    {
        $jsonFile = tempnam('/tmp', '');

        file_put_contents($jsonFile, json_encode(array(
            'foo.css' => 'foo-123.css',
        )));

        $vs = new \Rj\FrontendBundle\Asset\ManifestVersionStrategy(new JsonManifestLoader($jsonFile));

        $this->assertEquals('', $vs->getVersion('foo.css'));

        $this->assertEquals('foo-123.css', $vs->applyVersion('foo.css'));
        $this->assertEquals('bar.css', $vs->applyVersion('bar.css'));

        unlink($jsonFile);
    }

    public function setUp()
    {
        if (!Util::hasAssetComponent()) {
            return $this->markTestSkipped();
        }
    }
}
