<?php

namespace Rj\FrontendBundle\Manifest\Loader;

interface ManifestLoaderInterface
{
    public function load();

    public function getPath();
}
