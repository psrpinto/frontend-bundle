<?php

namespace Rj\FrontendBundle\Tests\Functional;

use Rj\FrontendBundle\Tests\Functional\TestApp\app\AppKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    protected function setUp()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().'/rj_frontend/');
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return self::$kernel = new AppKernel($options);
    }

    /**
     * @param string $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return self::$kernel->getContainer();
    }
}
