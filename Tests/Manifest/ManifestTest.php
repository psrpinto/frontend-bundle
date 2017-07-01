<?php

namespace Rj\FrontendBundle\Tests\Asset;

use PHPUnit_Framework_TestCase;
use Rj\FrontendBundle\Manifest\Manifest;

class ManifestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Manifest
     */
    private $manifest;

    public function setUp()
    {
        $this->manifest = new Manifest([
            'foo.css' => 'foo-123.css',
            'bar.js' => 'bar-123.js',
        ]);
    }

    public function testHas()
    {
        $this->assertFalse($this->manifest->has('foo'));
        $this->assertTrue($this->manifest->has('foo.css'));
    }

    public function testGet()
    {
        $this->assertEmpty($this->manifest->get('foo'));
        $this->assertEquals('foo-123.css', $this->manifest->get('foo.css'));
    }

    public function testAll()
    {
        $entries = $this->manifest->all();

        $this->assertCount(2, $entries);
        $this->assertEquals('foo-123.css', $entries['foo.css']);
        $this->assertEquals('bar-123.js', $entries['bar.js']);
    }
}
