<?php

namespace Rj\FrontendBundle\Tests\Asset;

use Rj\FrontendBundle\Manifest\Loader\JsonManifestLoader;

class JsonManifestLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $manifest = $this->load(array('foo.css' => 'foo-123.css'));

        $this->assertEquals('foo-123.css', $manifest->get('foo.css'));
    }

    public function testLoadRootKey()
    {
        $manifest = $this->load(array('foo.css' => 'foo-123.css'), 'assets');

        $this->assertEquals('foo-123.css', $manifest->get('foo.css'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadRootKeyNotFound()
    {
        file_put_contents($this->path, json_encode(array('foo.css' => 'foo-123.css')));

        $loader = new JsonManifestLoader($this->path, 'assets');
        $loader->load();
    }

    public function testGetPath()
    {
        $loader = new JsonManifestLoader($this->path);

        $this->assertEquals($this->path, $loader->getPath());
    }

    public function setUp()
    {
        $this->path = tempnam('/tmp', '');
    }

    public function tearDown()
    {
        unlink($this->path);
    }

    private function load($entries, $rootKey = null)
    {
        if ($rootKey) {
            $entries = array($rootKey => $entries);
        }

        file_put_contents($this->path, json_encode($entries));

        $loader = new JsonManifestLoader($this->path, $rootKey);

        return $loader->load();
    }
}
