<?php

namespace Rj\FrontendBundle;

use Rj\FrontendBundle\DependencyInjection\Compiler\AssetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RjFrontendBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (class_exists('Symfony\Component\Asset\Packages')) {
            // The Asset component is only available in Symfony >= v2.7
            $container->addCompilerPass(new AssetCompilerPass());
        }
    }
}
