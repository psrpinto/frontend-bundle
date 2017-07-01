<?php

namespace Rj\FrontendBundle\Manifest\Loader;

use Rj\FrontendBundle\Manifest\Manifest;

interface ManifestLoaderInterface
{
    /**
     * @return Manifest
     */
    public function load();

    /**
     * @return string
     */
    public function getPath();
}
