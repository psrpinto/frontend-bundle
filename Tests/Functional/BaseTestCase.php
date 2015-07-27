<?php

namespace Rj\FrontendBundle\Tests\Functional;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    protected static function createKernel(array $options = array())
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
        return self::$kernel->getContainer()->get($id);
    }
}
