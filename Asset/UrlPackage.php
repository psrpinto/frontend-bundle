<?php

namespace Rj\FrontendBundle\Asset;

use Symfony\Component\Asset\UrlPackage as BaseUrlPackage;

class UrlPackage extends BaseUrlPackage
{
    public function __construct($baseUrls, VersionStrategy $versionStrategy)
    {
        parent::__construct($baseUrls, $versionStrategy);
    }
}
