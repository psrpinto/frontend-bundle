<?php

namespace Rj\FrontendBundle;

use Rj\FrontendBundle\Util\Util;
use Rj\FrontendBundle\DependencyInjection\Compiler\AssetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RjFrontendBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (Util::hasAssetComponent()) {
            $container->addCompilerPass(new AssetCompilerPass());
        }
    }
}
