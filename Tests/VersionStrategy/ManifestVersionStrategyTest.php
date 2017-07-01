<?php

namespace Rj\FrontendBundle\Tests\VersionStrategy;

use PHPUnit_Framework_TestCase;
use Rj\FrontendBundle\VersionStrategy\ManifestVersionStrategy;
use Rj\FrontendBundle\Manifest\Loader\JsonManifestLoader;

class ManifestVersionStrategyTest extends PHPUnit_Framework_TestCase
{
    public function testApplyVersion()
    {
        $jsonFile = tempnam('/tmp', '');

        file_put_contents($jsonFile, json_encode([
            'foo.css' => 'foo-123.css',
        ]));

        $vs = new ManifestVersionStrategy(new JsonManifestLoader($jsonFile));

        $this->assertEquals('foo-123.css', $vs->applyVersion('foo.css'));
        $this->assertEquals('bar.css', $vs->applyVersion('bar.css'));

        unlink($jsonFile);
    }
}
