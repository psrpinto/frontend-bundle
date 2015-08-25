<?php

namespace Rj\FrontendBundle\Package;

class FallbackPackage
{
    private $patterns;
    private $package;
    private $fallback;

    public function __construct($patterns, $package)
    {
        $this->patterns = $patterns;
        $this->package = $package;
    }

    public function setFallback($fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    public function getVersion($path)
    {
        if ($this->mustFallback($path)) {
            return $this->fallback->getVersion($path);
        }

        return $this->package->getVersion($path);
    }

    public function getUrl($path, $version = null)
    {
        if ($this->mustFallback($path)) {
            return $this->fallback->getUrl($path, $version);
        }

        return $this->package->getUrl($path);
    }

    protected function mustFallback($path)
    {
        foreach ($this->patterns as $pattern) {
            if (1 === preg_match("/$pattern/", $path)) {
                return true;
            }
        }

        return false;
    }
}
