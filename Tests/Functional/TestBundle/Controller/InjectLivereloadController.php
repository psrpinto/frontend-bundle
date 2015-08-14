<?php

namespace Rj\FrontendBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class InjectLivereloadController extends Controller
{
    public function injectAction()
    {
        return new Response('foo</body>');
    }

    public function notInjectAction()
    {
        return new Response('foo');
    }
}
