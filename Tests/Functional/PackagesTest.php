<?php

namespace Rj\FrontendBundle\Tests\Functional;

class PackagesTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackage()
    {
        $this->doTest('packages_default', '/css/foo.css');
    }

    /**
     * @runInSeparateProcess
     */
    public function testCustomPackageNoManifest()
    {
        $this->doTest('packages_custom', '/css/foo.css', array(
            'packages' => array(
                'app' => array(),
            ),
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testCustomPackageWithManifest()
    {
        $manifest = tempnam('/tmp', '');

        file_put_contents($manifest, json_encode(array(
            'css/foo.css' => 'css/foo-123.css',
        )));

        $this->doTest('packages_custom', '/css/foo-123.css', array(
            'packages' => array(
                'app' => array(
                    'manifest' => array(
                        'enabled' => true,
                        'path' => $manifest,
                    ),
                ),
            ),
        ));

        unlink($manifest);
    }

    private function doTest($route, $expected, $config = array())
    {
        $client = $this->createClient($config);
        $router = $this->get('router');

        $crawler = $client->request('GET', $router->generate($route));

        $response = $client->getResponse()->getContent();
        $this->assertEquals($expected, $response);
    }
}
