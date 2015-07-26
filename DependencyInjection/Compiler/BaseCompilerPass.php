<?php

namespace Rj\FrontendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

abstract class BaseCompilerPass implements CompilerPassInterface
{
    protected function getConfig($container)
    {
        return $container->getParameter($this->getAlias().'.__config');
    }

    protected function getAlias()
    {
        return 'rj_frontend';
    }
}
