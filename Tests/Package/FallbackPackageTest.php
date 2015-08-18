<?php

namespace Rj\FrontendBundle\Tests\Package;

use Rj\FrontendBundle\Util\Util;
use Rj\FrontendBundle\Package\FallbackPackage;

class FallbackPackageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetVersio()
    {
        $this->package
            ->method('getVersion')
            ->willReturn('package')
        ;

        $this->default
            ->method('getVersion')
            ->willReturn('default')
        ;

        $this->assertEquals('package', $this->fallbackPackage->getVersion('css/foo.css'));
        $this->assertEquals('default', $this->fallbackPackage->getVersion('must_fallback/foo.css'));
    }

    public function testGetUrl()
    {
        $this->package
            ->method('getUrl')
            ->willReturn('package')
        ;

        $this->default
            ->method('getUrl')
            ->willReturn('default')
        ;

        $this->assertEquals('package', $this->fallbackPackage->getUrl('css/foo.css'));
        $this->assertEquals('default', $this->fallbackPackage->getUrl('must_fallback/foo.css'));
    }

    public function setUp()
    {
        if (Util::hasAssetComponent()) {
            $packageBuilder = $this->getMockBuilder('Rj\FrontendBundle\Asset\PathPackage');
            $defaultBuilder = $this->getMockBuilder('Symfony\Component\Asset\PathPackage');
        } else {
            $packageBuilder = $this->getMockBuilder('Rj\FrontendBundle\Templating\PathPackage');
            $defaultBuilder = $this->getMockBuilder('Symfony\Component\Templating\Asset\PathPackage');
        }

        $this->package = $packageBuilder
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->default = $defaultBuilder
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->fallbackPackage = new FallbackPackage(array(
            'must_fallback\/',
        ));

        $this->fallbackPackage
            ->setPackage($this->package)
            ->setFallback($this->default)
        ;
    }
}
