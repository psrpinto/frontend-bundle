<?php

namespace Rj\FrontendBundle\VersionStrategy;

use Rj\FrontendBundle\Manifest\Loader\ManifestLoaderInterface;

class ManifestVersionStrategy implements VersionStrategyInterface
{
    private $loader;
    private $manifest = null;

    public function __construct(ManifestLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function getVersion($path)
    {
        return '';
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
