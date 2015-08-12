<?php

namespace Rj\FrontendBundle\Manifest;

use Rj\FrontendBundle\Manifest\Loader\ManifestLoaderInterface;

class ManifestVersioner
{
    private $loader;
    private $manifest = null;

    public function __construct(ManifestLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function applyVersion($path)
    {
        if ($this->manifest === null) {
            $this->manifest = $this->loader->load();
        }

        if (!$this->manifest->has($path)) {
            return $path;
        }

        return $this->manifest->get($path);
    }
}
