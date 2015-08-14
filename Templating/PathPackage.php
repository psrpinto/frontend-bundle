<?php

namespace Rj\FrontendBundle\Templating;

use Rj\FrontendBundle\VersionStrategy\VersionStrategyInterface;
use Symfony\Component\Templating\Asset\PathPackage as BasePathPackage;

class PathPackage extends BasePathPackage
{
    private $delegate;

    public function __construct($basePath, VersionStrategyInterface $delegate)
    {
        parent::__construct($basePath);

        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->delegate->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    protected function applyVersion($path, $version = null)
    {
        return $this->delegate->applyVersion($path);
    }
}
