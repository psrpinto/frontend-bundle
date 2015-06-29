<?php

namespace Rj\FrontendBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LiveReloadListener implements EventSubscriberInterface
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false === $pos) {
            return;
        }

        $script = '<script src="'.$this->url.'"></script>';
        $content = substr($content, 0, $pos).$script.substr($content, $pos);
        $response->setContent($content);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
        );
    }
}
