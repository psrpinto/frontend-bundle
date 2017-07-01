<?php

namespace Rj\FrontendBundle\Tests\Functional;

class InjectLivereloadTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testDisabled()
    {
        $client = $this->createClient([
            'rj_frontend' => [
                'livereload' => false,
            ],
        ]);

        $router = $this->get('router');

        $client->request('GET', $router->generate('livereload_inject'));

        $response = $client->getResponse()->getContent();
        $this->assertEquals('foo</body>', $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testEnabled()
    {
        $client = $this->createClient([
            'rj_frontend' => [
                'livereload' => [
                    'enabled' => true,
                    'url' => '://foo',
                ],
            ],
        ]);

        $router = $this->get('router');

        $client->request('GET', $router->generate('livereload_inject'));

        $response = $client->getResponse()->getContent();
        $this->assertEquals('foo<script src="://foo"></script></body>', $response);
    }
}
