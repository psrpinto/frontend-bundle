<?php

namespace Rj\FrontendBundle\Asset;

use Symfony\Component\Asset\PathPackage as BasePathPackage;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class PathPackage extends BasePathPackage
{
    public function __construct($basePath, VersionStrategyInterface $versionStrategy)
    {
        parent::__construct($basePath, $versionStrategy);
    }
}
