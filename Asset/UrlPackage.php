<?php

namespace Rj\FrontendBundle\Asset;

use Symfony\Component\Asset\UrlPackage as BaseUrlPackage;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class UrlPackage extends BaseUrlPackage
{
    public function __construct($baseUrls, VersionStrategyInterface $versionStrategy)
    {
        parent::__construct($baseUrls, $versionStrategy);
    }
}
