<?php

namespace Rj\FrontendBundle\VersionStrategy;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class EmptyVersionStrategy implements VersionStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion($path)
    {
        return $path;
    }
}
