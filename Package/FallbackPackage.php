<?php

namespace Rj\FrontendBundle\Package;

use Symfony\Component\Asset\PackageInterface;

class FallbackPackage implements PackageInterface
{
    /**
     * @var array
     */
    private $patterns;

    /**
     * @var PackageInterface
     */
    private $package;

    /**
     * @var PackageInterface
     */
    private $fallback;

    /**
     * @param array            $patterns
     * @param PackageInterface $package
     */
    public function __construct(array $patterns, PackageInterface $package)
    {
        $this->patterns = $patterns;
        $this->package = $package;
    }

    /**
     * @param PackageInterface $fallback
     *
     * @return $this
     */
    public function setFallback(PackageInterface $fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($path)
    {
        if ($this->mustFallback($path)) {
            return $this->fallback->getVersion($path);
        }

        return $this->package->getVersion($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($path, $version = null)
    {
        if ($this->mustFallback($path)) {
            return $this->fallback->getUrl($path);
        }

        return $this->package->getUrl($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
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
