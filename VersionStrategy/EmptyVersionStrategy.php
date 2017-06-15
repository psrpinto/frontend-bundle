<?php

namespace Rj\FrontendBundle\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class EmptyVersionStrategy implements VersionStrategyInterface
{
    public function getVersion($path)
    {
        return '';
    }

    public function applyVersion($path)
    {
        return $path;
    }
}
