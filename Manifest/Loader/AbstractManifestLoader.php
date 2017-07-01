<?php

namespace Rj\FrontendBundle\Manifest\Loader;

use Rj\FrontendBundle\Manifest\Manifest;

abstract class AbstractManifestLoader implements ManifestLoaderInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var null|string
     */
    private $rootKey;

    /**
     * @param string $path
     *
     * @return mixed
     */
    abstract protected function parse($path);

    /**
     * @param string      $path
     * @param null|string $rootKey
     */
    public function __construct($path, $rootKey = null)
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("The manifest file '$path' could not be found");
        }

        $this->path = $path;
        $this->rootKey = $rootKey;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $entries = $this->parse($this->path);

        if (!empty($this->rootKey)) {
            if (!isset($entries[$this->rootKey])) {
                throw new \InvalidArgumentException('Manifest file contains no '.$this->rootKey.' key');
            }

            $entries = $entries[$this->rootKey];
        }

        return new Manifest($entries);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}
