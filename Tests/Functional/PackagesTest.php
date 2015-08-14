<?php

namespace Rj\FrontendBundle\Tests\Functional;

class PackagesTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testBundleDisabled()
    {
        $this->doTest('packages_default', '/css/foo.css');
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackage()
    {
        $this->doTest('packages_default', '/foo/css/foo.css', array(
            'packages' => array(
                'default' => array(
                    'prefixes' => 'foo',
                ),
            ),
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testPathPackage()
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
    public function testUrlPackage()
    {
        $this->doTest('packages_custom', 'http://foo/css/foo.css', array(
            'packages' => array(
                'app' => array(
                    'prefixes' => 'http://foo',
                ),
            ),
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testUrlPackageSsl()
    {
        $this->doTest('packages_custom', 'https://foo/css/foo.css', array(
            'packages' => array(
                'app' => array(
                    'prefixes' => 'https://foo',
                ),
            ),
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testUrlPackageNoProtocol()
    {
        $this->doTest('packages_custom', '//foo/css/foo.css', array(
            'packages' => array(
                'app' => array(
                    'prefixes' => '//foo',
                ),
            ),
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testPackageWithManifest()
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
