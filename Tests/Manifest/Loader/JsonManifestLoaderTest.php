<?php

namespace Rj\FrontendBundle\Tests\Asset;

use PHPUnit_Framework_TestCase;
use Rj\FrontendBundle\Manifest\Loader\JsonManifestLoader;
use Rj\FrontendBundle\Manifest\Manifest;

class JsonManifestLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $path;

    public function setUp()
    {
        $this->path = tempnam('/tmp', '');
    }

    public function tearDown()
    {
        unlink($this->path);
    }

    public function testLoad()
    {
        $manifest = $this->load(['foo.css' => 'foo-123.css']);

        $this->assertEquals('foo-123.css', $manifest->get('foo.css'));
    }

    public function testLoadRootKey()
    {
        $manifest = $this->load(['foo.css' => 'foo-123.css'], 'assets');

        $this->assertEquals('foo-123.css', $manifest->get('foo.css'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadRootKeyNotFound()
    {
        file_put_contents($this->path, json_encode(['foo.css' => 'foo-123.css']));

        $loader = new JsonManifestLoader($this->path, 'assets');
        $loader->load();
    }

    public function testGetPath()
    {
        $loader = new JsonManifestLoader($this->path);

        $this->assertEquals($this->path, $loader->getPath());
    }

    /**
     * @param array       $entries
     * @param null|string $rootKey
     *
     * @return Manifest
     */
    private function load(array $entries, $rootKey = null)
    {
        if ($rootKey) {
            $entries = [$rootKey => $entries];
        }

        file_put_contents($this->path, json_encode($entries));

        $loader = new JsonManifestLoader($this->path, $rootKey);

        return $loader->load();
    }
}
