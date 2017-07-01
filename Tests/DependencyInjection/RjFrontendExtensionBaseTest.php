<?php

namespace Rj\FrontendBundle\Tests\DependencyInjection;

use Rj\FrontendBundle\DependencyInjection\RjFrontendExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

abstract class RjFrontendExtensionBaseTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [new RjFrontendExtension()];
    }

    protected function namespaceService($id)
    {
        return "rj_frontend.$id";
    }
}
