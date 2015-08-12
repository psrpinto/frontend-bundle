<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;

abstract class BaseCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function namespaceService($id)
    {
        return "rj_frontend.$id";
    }
}
