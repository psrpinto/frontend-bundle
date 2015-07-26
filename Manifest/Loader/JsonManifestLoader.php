<?php

namespace Rj\FrontendBundle\Manifest\Loader;

class JsonManifestLoader extends AbstractManifestLoader
{
    /**
     * {@inheritdoc}
     */
    protected function parse($path)
    {
        $entries = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to parse json manifest file ($path): ".json_last_error_msg());
        }

        return $entries;
    }
}
