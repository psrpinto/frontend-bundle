<?php

namespace Rj\FrontendBundle\Util;

class Util
{
    public static function hasAssetComponent()
    {
        return class_exists('Symfony\Component\Asset\Packages');
    }
}
