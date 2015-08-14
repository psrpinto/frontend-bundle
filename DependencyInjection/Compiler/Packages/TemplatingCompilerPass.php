<?php

namespace Rj\FrontendBundle\DependencyInjection\Compiler\Packages;

class TemplatingCompilerPass extends BasePackagesCompilerPass
{
    protected function getTaggedPackages($container)
    {
        return $container->findTaggedServiceIds($this->namespaceService('package.templating'));
    }

    protected function getPackagesService($container)
    {
        if (!$container->hasDefinition('templating.helper.assets')) {
            throw new \LogicException("The 'assets' templating helper is not registered in the container");
        }

        return $container->getDefinition('templating.helper.assets');
    }
}
