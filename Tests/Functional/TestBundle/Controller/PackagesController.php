<?php

namespace Rj\FrontendBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PackagesController extends Controller
{
    public function defaultAction()
    {
        return $this->render('TestBundle:Packages:default.html.php');
    }

    public function customAction()
    {
        return $this->render('TestBundle:Packages:custom.html.php');
    }
}
