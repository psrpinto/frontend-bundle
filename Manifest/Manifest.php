<?php

namespace Rj\FrontendBundle\Manifest;

class Manifest
{
    /**
     * @var array
     */
    private $entries;

    /**
     * @param array $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->entries;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return array_key_exists($path, $this->entries);
    }

    /**
     * @param string $path
     *
     * @return null|string
     */
    public function get($path)
    {
        if (!$this->has($path)) {
            return null;
        }

        return $this->entries[$path];
    }
}
