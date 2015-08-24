<?php

namespace Rj\FrontendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

abstract class BaseCompilerPass implements CompilerPassInterface
{
    protected function namespaceService($id)
    {
        return "rj_frontend.$id";
    }
}
