<?php

namespace Rj\FrontendBundle\Asset;

use Symfony\Component\Asset\PathPackage as BasePathPackage;

class PathPackage extends BasePathPackage
{
    public function __construct($basePath, VersionStrategy $versionStrategy)
    {
        parent::__construct($basePath, $versionStrategy);
    }
}
