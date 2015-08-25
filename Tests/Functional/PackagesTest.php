<?php

namespace Rj\FrontendBundle\Tests\Functional;

class PackagesTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testDontOverrideDefaultPackage()
    {
        $this->doTest('packages_default', '/css/foo.css', array(
            'override_default_package' => false,
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackage()
    {
        $this->doTest('packages_default', '/foo/css/foo.css', array(
            'prefix' => 'foo',
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackageWithManifest()
    {
        $manifest = tempnam('/tmp', '');

        file_put_contents($manifest, json_encode(array(
            'css/foo.css' => 'css/foo-123.css',
        )));

        $this->doTest('packages_default', '/app_prefix/css/foo-123.css', array(
            'prefix' => 'app_prefix',
            'manifest' => array(
                'enabled' => true,
                'path' => $manifest,
            ),
        ));

        unlink($manifest);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackageWithManifestWithInferredPath()
    {
        // it uses the manifest file in TestApp/web/assets/manifest.json

        $this->doTest('packages_default', '/assets/css/foo-123.css', array(
            'manifest' => array(
                'enabled' => true,
            ),
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testFallbackPackage()
    {
        $this->doTest('packages_fallback', '/bundles/foo.css', array(
            'prefix' => 'foo',
        ));
    }

    /**
     * @runInSeparateProcess
     */
    public function testPathPackage()
    {
        $this->doTest('packages_custom', '/app_prefix/css/foo.css', array(
            'packages' => array(
                'app' => array(
                    'prefix' => 'app_prefix',
                ),
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
                    'prefix' => 'http://foo',
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
                    'prefix' => 'https://foo',
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
                    'prefix' => '//foo',
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

        $this->doTest('packages_custom', '/app_prefix/css/foo-123.css', array(
            'packages' => array(
                'app' => array(
                    'prefix' => 'app_prefix',
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

        $client->request('GET', $router->generate($route));

        $response = $client->getResponse()->getContent();
        $this->assertEquals($expected, $response);
    }
}
