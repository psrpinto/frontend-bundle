<?php

namespace Rj\FrontendBundle\Tests\EventListener;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Rj\FrontendBundle\EventListener\InjectLiveReloadListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;

class InjectLiveReloadListenerTest extends PHPUnit_Framework_TestCase
{
    public function testDontInjectScriptIfNotMasterRequest()
    {
        $response = new Response('foo</body>');
        $response->headers->set('X-Debug-Token', 'xxxxxxxx');

        $listener = new InjectLiveReloadListener('bar');
        $listener->onKernelResponse(new FilterResponseEvent(
            $this->mockKernel(),
            $this->mockRequest(),
            HttpKernelInterface::SUB_REQUEST,
            $response
        ));

        $this->assertEquals('foo</body>', $response->getContent());
    }

    public function testDontInjectScriptIfXmlHttpRequest()
    {
        $response = new Response('foo</body>');
        $response->headers->set('X-Debug-Token', 'xxxxxxxx');

        $listener = new InjectLiveReloadListener('bar');
        $listener->onKernelResponse(new FilterResponseEvent(
            $this->mockKernel(),
            $this->mockRequest(true),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        ));

        $this->assertEquals('foo</body>', $response->getContent());
    }

    public function testDontInjectScriptIfXDebugTokenHeaderNotPresent()
    {
        $response = new Response('foo</body>');

        $listener = new InjectLiveReloadListener('bar');
        $listener->onKernelResponse(new FilterResponseEvent(
            $this->mockKernel(),
            $this->mockRequest(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        ));

        $this->assertEquals('foo</body>', $response->getContent());
    }

    public function testDontInjectScriptIfNoClosingBodyTag()
    {
        $response = new Response('foo');
        $response->headers->set('X-Debug-Token', 'xxxxxxxx');

        $listener = new InjectLiveReloadListener('bar');
        $listener->onKernelResponse(new FilterResponseEvent(
            $this->mockKernel(),
            $this->mockRequest(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        ));

        $this->assertEquals('foo', $response->getContent());
    }

    public function testInjectScript()
    {
        $response = new Response('foo</body>');
        $response->headers->set('X-Debug-Token', 'xxxxxxxx');

        $listener = new InjectLiveReloadListener('bar');
        $listener->onKernelResponse(new FilterResponseEvent(
            $this->mockKernel(),
            $this->mockRequest(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        ));

        $this->assertEquals('foo<script src="bar"></script></body>', $response->getContent());
    }

    /**
     * @param bool $isXmlHttpRequest
     *
     * @return Request|PHPUnit_Framework_MockObject_MockObject
     */
    private function mockRequest($isXmlHttpRequest = false)
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods(['isXmlHttpRequest'])
            ->getMock()
        ;

        $request->expects($this->any())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue($isXmlHttpRequest));

        return $request;
    }

    /**
     * @return Kernel|PHPUnit_Framework_MockObject_MockObject
     */
    private function mockKernel()
    {
        return $this->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
