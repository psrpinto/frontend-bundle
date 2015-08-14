<?php

namespace Rj\FrontendBundle\DependencyInjection\Compiler\Packages;

class AssetCompilerPass extends BasePackagesCompilerPass
{
    protected function getTaggedPackages($container)
    {
        return $container->findTaggedServiceIds($this->namespaceService('package.asset'));
    }

    protected function getPackagesService($container)
    {
        if (!$container->hasDefinition('assets.packages')) {
            throw new \LogicException('The Asset component is not registered in the container');
        }

        return $container->getDefinition('assets.packages');
    }
}
