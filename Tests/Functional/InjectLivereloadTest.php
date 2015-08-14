<?php

namespace Rj\FrontendBundle\Tests\Functional;

class InjectLivereloadTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testDisabled()
    {
        $client = $this->createClient(array(
            'livereload' => false,
        ));

        $router = $this->get('router');

        $client->request('GET', $router->generate('livereload_inject'));

        $response = $client->getResponse()->getContent();
        $this->assertEquals('foo</body>', $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testInjectScript()
    {
        $client = $this->createClient(array(
            'livereload' => array(
                'url' => 'foo',
            ),
        ));

        $router = $this->get('router');

        $client->request('GET', $router->generate('livereload_inject'));

        $response = $client->getResponse()->getContent();
        $this->assertEquals('foo<script src="foo"></script></body>', $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testNotInjectScript()
    {
        $client = $this->createClient(array(
            'livereload' => array(
                'url' => 'foo',
            ),
        ));

        $router = $this->get('router');

        $client->request('GET', $router->generate('livereload_not_inject'));

        $response = $client->getResponse()->getContent();
        $this->assertEquals('foo', $response);
    }
}
