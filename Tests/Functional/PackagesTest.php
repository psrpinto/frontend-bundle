<?php

namespace Rj\FrontendBundle\Tests\Functional;

class PackagesTest extends BaseTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testDontOverrideDefaultPackage()
    {
        $this->doTest('packages_default', '/css/foo.css', [
            'rj_frontend' => [
                'override_default_package' => false,
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackage()
    {
        $this->doTest('packages_default', '/foo/css/foo.css', [
            'rj_frontend' => [
                'prefix' => 'foo',
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackageWithManifest()
    {
        $manifest = tempnam('/tmp', '');

        file_put_contents($manifest, json_encode([
            'css/foo.css' => 'css/foo-123.css',
        ]));

        $this->doTest('packages_default', '/app_prefix/css/foo-123.css', [
            'rj_frontend' => [
                'prefix' => 'app_prefix',
                'manifest' => [
                    'enabled' => true,
                    'path' => $manifest,
                ],
            ],
        ]);

        unlink($manifest);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDefaultPackageWithManifestWithInferredPath()
    {
        // it uses the manifest file in TestApp/web/assets/manifest.json

        $this->doTest('packages_default', '/assets/css/foo-123.css', [
            'rj_frontend' => [
                'manifest' => [
                    'enabled' => true,
                ],
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFallbackPackage()
    {
        $this->doTest('packages_fallback', '/bundles/foo.css', [
            'rj_frontend' => [
                'prefix' => 'foo',
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testPathPackage()
    {
        $this->doTest('packages_custom', '/app_prefix/css/foo.css', [
            'rj_frontend' => [
                'packages' => [
                    'app' => [
                        'prefix' => 'app_prefix',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testUrlPackage()
    {
        $this->doTest('packages_custom', 'http://foo/css/foo.css', [
            'rj_frontend' => [
                'packages' => [
                    'app' => [
                        'prefix' => 'http://foo',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testUrlPackageSsl()
    {
        $this->doTest('packages_custom', 'https://foo/css/foo.css', [
            'rj_frontend' => [
                'packages' => [
                    'app' => [
                        'prefix' => 'https://foo',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testUrlPackageNoProtocol()
    {
        $this->doTest('packages_custom', '//foo/css/foo.css', [
            'rj_frontend' => [
                'packages' => [
                    'app' => [
                        'prefix' => '//foo',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testPackageWithManifest()
    {
        $manifest = tempnam('/tmp', '');

        file_put_contents($manifest, json_encode([
            'css/foo.css' => 'css/foo-123.css',
        ]));

        $this->doTest('packages_custom', '/app_prefix/css/foo-123.css', [
            'rj_frontend' => [
                'packages' => [
                    'app' => [
                        'prefix' => 'app_prefix',
                        'manifest' => [
                            'enabled' => true,
                            'path' => $manifest,
                        ],
                    ],
                ],
            ],
        ]);

        unlink($manifest);
    }

    private function doTest($route, $expected, $config = [])
    {
        $client = $this->createClient($config);
        $router = $this->get('router');

        $client->request('GET', $router->generate($route));

        $response = $client->getResponse()->getContent();
        $this->assertEquals($expected, $response);
    }
}
