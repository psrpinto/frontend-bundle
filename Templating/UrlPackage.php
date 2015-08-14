<?php

namespace Rj\FrontendBundle\Templating;

use Rj\FrontendBundle\VersionStrategy\VersionStrategyInterface;
use Symfony\Component\Templating\Asset\UrlPackage as BaseUrlPackage;

class UrlPackage extends BaseUrlPackage
{
    private $delegate;

    public function __construct($baseUrls, VersionStrategyInterface $delegate)
    {
        parent::__construct($baseUrls);

        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->delegate->getVersion(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyVersion($path, $version = null)
    {
        return $this->delegate->applyVersion($path);
    }
}
