<?php

namespace Rj\FrontendBundle\VersionStrategy;

interface VersionStrategyInterface
{
    public function getVersion($path);
    public function applyVersion($path);
}
