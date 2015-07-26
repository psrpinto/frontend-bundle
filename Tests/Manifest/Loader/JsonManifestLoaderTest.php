<?php

namespace Rj\FrontendBundle\Tests\Asset;

use Rj\FrontendBundle\Manifest\Loader\JsonManifestLoader;

class JsonManifestLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        file_put_contents($this->path, json_encode(array(
            'foo.css' => 'foo-123.css',
        )));

        $loader = new JsonManifestLoader($this->path);
        $manifest = $loader->load();

        $this->assertEquals($manifest->get('foo.css'), 'foo-123.css');
    }

    public function testLoadRootKey()
    {
        $rootKey = 'assets';

        file_put_contents($this->path, json_encode(array(
            $rootKey => array(
                'foo.css' => 'foo-123.css',
            ),
        )));

        $loader = new JsonManifestLoader($this->path, $rootKey);
        $manifest = $loader->load();

        $this->assertEquals($manifest->get('foo.css'), 'foo-123.css');
    }

    public function testGetPath()
    {
        file_put_contents($this->path, json_encode(array(
            'foo.css' => 'foo-123.css',
        )));

        $loader = new JsonManifestLoader($this->path);

        $this->assertEquals($loader->getPath(), $this->path);
    }

    public function setUp()
    {
        $this->path = tempnam('/tmp', '');
    }

    public function tearDown()
    {
        unlink($this->path);
    }
}
