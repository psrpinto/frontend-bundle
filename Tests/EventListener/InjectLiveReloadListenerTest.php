<?php

namespace Rj\FrontendBundle\Tests\EventListener;

use Rj\FrontendBundle\EventListener\InjectLiveReloadListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class InjectLiveReloadListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testDontInjectScriptIfNotMasterRequest()
    {
        $response = new Response('foo</body>');
        $response->headers->set('X-Debug-Token', 'xxxxxxxx');

        $listener = new InjectLiveReloadListener('bar');
        $listener->onKernelResponse(new FilterResponseEvent(
            $this->getKernelMock(),
            $this->getRequestMock(),
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
            $this->getKernelMock(),
            $this->getRequestMock(true),
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
            $this->getKernelMock(),
            $this->getRequestMock(),
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
            $this->getKernelMock(),
            $this->getRequestMock(),
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
            $this->getKernelMock(),
            $this->getRequestMock(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        ));

        $this->assertEquals('foo<script src="bar"></script></body>', $response->getContent());
    }

    protected function getRequestMock($isXmlHttpRequest = false)
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('isXmlHttpRequest'))
            ->getMock()
        ;

        $request->expects($this->any())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue($isXmlHttpRequest));

        return $request;
    }

    protected function getKernelMock()
    {
        return $this->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
