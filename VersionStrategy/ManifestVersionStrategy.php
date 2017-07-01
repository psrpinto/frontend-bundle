<?php

namespace Rj\FrontendBundle\VersionStrategy;

use Rj\FrontendBundle\Manifest\Loader\ManifestLoaderInterface;
use Rj\FrontendBundle\Manifest\Manifest;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ManifestVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var ManifestLoaderInterface
     */
    private $loader;

    /**
     * @var null|Manifest
     */
    private $manifest = null;

    /**
     * @param ManifestLoaderInterface $loader
     */
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
