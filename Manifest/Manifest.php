<?php

namespace Rj\FrontendBundle\Manifest;

class Manifest
{
    private $entries;

    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    public function all()
    {
        return $this->entries;
    }

    public function has($path)
    {
        return array_key_exists($path, $this->entries);
    }

    public function get($path)
    {
        if (!$this->has($path)) {
            return;
        }

        return $this->entries[$path];
    }
}
