<?php

namespace Rj\FrontendBundle\Tests\Asset;

use Rj\FrontendBundle\Manifest\Manifest;

class ManifestTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $this->assertEquals($this->manifest->has('foo'), false);
        $this->assertEquals($this->manifest->has('foo.css'), true);
    }

    public function testGet()
    {
        $this->assertEquals($this->manifest->get('foo'), '');
        $this->assertEquals($this->manifest->get('foo.css'), 'foo-123.css');
    }

    public function setUp()
    {
        $this->manifest = new Manifest(array(
            'foo.css' => 'foo-123.css',
        ));
    }
}
