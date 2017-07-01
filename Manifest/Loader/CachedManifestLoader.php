<?php

namespace Rj\FrontendBundle\Manifest\Loader;

use Rj\FrontendBundle\Manifest\Manifest;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class CachedManifestLoader implements ManifestLoaderInterface
{
    private $loader;
    private $cache;

    public function __construct($cacheDir, $debug, ManifestLoaderInterface $loader)
    {
        $cachePath = $cacheDir.'/'.hash('sha1', $loader->getPath()).'.php.cache';

        $this->cache = new ConfigCache($cachePath, $debug);
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        if (!$this->cache->isFresh()) {
            $resource = new FileResource($this->getPath());
            $entries = $this->loader->load()->all();

            $this->cache->write(sprintf('<?php return %s;', var_export($entries, true)), [$resource]);
        } else {
            $entries = include $this->cache->getPath();
        }

        return new Manifest($entries);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $this->loader->getPath();
    }
}
