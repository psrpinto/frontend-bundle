<?php

namespace Rj\FrontendBundle\Asset;

use Rj\FrontendBundle\Manifest\Loader\ManifestLoaderInterface;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ManifestVersionStrategy implements VersionStrategyInterface
{
    private $loader;
    private $manifest = null;

    public function __construct(ManifestLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
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
