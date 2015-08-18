<?php

namespace Rj\FrontendBundle\Asset;

use Symfony\Component\Asset\UrlPackage as BaseUrlPathPackage;

class UrlPackage extends BaseUrlPathPackage
{
    public function __construct($baseUrls, VersionStrategy $versionStrategy)
    {
        parent::__construct($baseUrls, $versionStrategy);
    }
}
