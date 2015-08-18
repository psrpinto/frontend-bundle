<?php

namespace Rj\FrontendBundle\Templating;

use Rj\FrontendBundle\Package\FallbackPackage as BaseFallbackPackage;
use Symfony\Component\Templating\Asset\PackageInterface;

class FallbackPackage extends BaseFallbackPackage implements PackageInterface
{
    public function getVersion($path = null)
    {
        return parent::getVersion(null);
    }
}
