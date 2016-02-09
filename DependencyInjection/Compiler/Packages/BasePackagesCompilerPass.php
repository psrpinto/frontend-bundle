<?php

namespace Rj\FrontendBundle\DependencyInjection\Compiler\Packages;

use Rj\FrontendBundle\DependencyInjection\Compiler\BaseCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class BasePackagesCompilerPass extends BaseCompilerPass
{
    abstract protected function getTaggedPackages($container);
    abstract protected function getPackagesService($container);

    public function process(ContainerBuilder $container)
    {
        $packages = array();
        $registeredPackages = $this->getRegisteredPackages($container);

        foreach ($this->getTaggedPackages($container) as $id => $tags) {
            if (empty($tags) || !isset($tags[0]['alias'])) {
                throw new \LogicException(
                    "The tag for the service with id '$id' must define an 'alias' attribute"
                );
            }

            $packageName = $tags[0]['alias'];

            if (isset($registeredPackages[$packageName])) {
                throw new \LogicException(
                    "A package named '$packageName' has already been registered"
                );
            }

            if (isset($packages[$packageName])) {
                throw new \LogicException(
                    "Multiple packages were found with alias '$packageName'. Package alias' must be unique"
                );
            }

            $packages[$packageName] = $id;
        }

        $this->addPackages($packages, $container);

        if ($container->hasDefinition($this->namespaceService('package.fallback'))) {
            $this->setDefaultPackage($container);
        }
    }

    protected function addPackages($packages, $container)
    {
        $packagesService = $this->getPackagesService($container);

        foreach ($packages as $name => $id) {
            $packagesService->addMethodCall(
                'addPackage',
                array($name, new Reference($id))
            );
        }
    }

    protected function setDefaultPackage($container)
    {
        $packagesService = $this->getPackagesService($container);
        $defaultPackage = $this->getRegisteredDefaultPackage($container);
        $fallbackPackageId = $this->namespaceService('package.fallback');

        $container->getDefinition($fallbackPackageId)->addMethodCall('setFallback', array($defaultPackage));

        $packagesService->replaceArgument(0, new Reference($fallbackPackageId));
    }

    /**
     * Retrieve packages that have already been registered.
     *
     * @return array with the packages' name as keys
     */
    protected function getRegisteredPackages($container)
    {
        $arguments = $this->getPackagesService($container)->getArguments();

        if (!isset($arguments[1]) || count($arguments[1]) < 2) {
            return array();
        }

        $argPackages = $arguments[1];

        $packages = array();
        $argCount = count($argPackages);
        for ($i = 0; $i < $argCount; $i++) {
            $packages[$argPackages[$i]] = $argPackages[++$i];
        }

        return $packages;
    }

    protected function getRegisteredDefaultPackage($container)
    {
        $arguments = $this->getPackagesService($container)->getArguments();

        if (!isset($arguments[0])) {
            return;
        }

        return $arguments[0];
    }
}
