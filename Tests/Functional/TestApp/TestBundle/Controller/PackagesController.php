<?php

namespace Rj\FrontendBundle\Tests\Functional\TestApp\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PackagesController extends Controller
{
    public function customAction()
    {
        return $this->render('TestBundle:Packages:custom.html.php');
    }

    public function defaultAction()
    {
        return $this->render('TestBundle:Packages:default.html.php');
    }

    public function fallbackAction()
    {
        return $this->render('TestBundle:Packages:fallback.html.php');
    }
}
