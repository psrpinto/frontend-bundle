<?php

namespace Rj\FrontendBundle\Asset;

use Rj\FrontendBundle\VersionStrategy\VersionStrategyInterface;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface as AssetVersionStrategyInterface;

class VersionStrategy implements AssetVersionStrategyInterface
{
    private $delegate;

    public function __construct(VersionStrategyInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        return $this->delegate->getVersion($path);
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion($path)
    {
        return $this->delegate->applyVersion($path);
    }
}
