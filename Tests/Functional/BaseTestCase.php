<?php

namespace Rj\FrontendBundle\Tests\Functional;

use Rj\FrontendBundle\Tests\Functional\TestApp\app\AppKernel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    protected static function createKernel(array $options = [])
    {
        return self::$kernel = new AppKernel($options);
    }

    protected function setUp()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().'/rj_frontend/');
    }

    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    protected function getContainer()
    {
        return self::$kernel->getContainer();
    }
}
