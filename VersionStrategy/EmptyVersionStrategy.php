<?php

namespace Rj\FrontendBundle\VersionStrategy;

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
