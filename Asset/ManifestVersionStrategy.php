<?php

namespace Rj\FrontendBundle\Asset;

use Rj\FrontendBundle\Manifest\ManifestVersioner;
use Rj\FrontendBundle\Manifest\Loader\ManifestLoaderInterface;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ManifestVersionStrategy implements VersionStrategyInterface
{
    private $versioner;

    public function __construct(ManifestLoaderInterface $loader)
    {
        $this->versioner = new ManifestVersioner($loader);
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
        return $this->versioner->applyVersion($path);
    }
}
