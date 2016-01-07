<?php

namespace Rj\FrontendBundle\Tests\Functional\TestApp\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class InjectLivereloadController extends Controller
{
    public function injectAction()
    {
        $response = new Response('foo</body>');

        $response->headers->set('X-Debug-Token', 'xxxxxxxx');

        return $response;
    }
}
