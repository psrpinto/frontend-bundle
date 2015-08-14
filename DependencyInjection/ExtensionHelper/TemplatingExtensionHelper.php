<?php

namespace Rj\FrontendBundle\DependencyInjection\ExtensionHelper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class TemplatingExtensionHelper extends BaseExtensionHelper
{
    public function __construct($alias, ContainerBuilder $container, $loader)
    {
        parent::__construct($alias, $container);

        $loader->load('templating.yml');
    }

    protected function createPackage($name, $prefixes, $manifest, $isUrl = false)
    {
        $package = $isUrl
            ? new DefinitionDecorator($this->namespaceService('templating.package.url'))
            : new DefinitionDecorator($this->namespaceService('templating.package.path'))
        ;

        return $package
            ->addArgument($isUrl ? $prefixes : $prefixes[0])
            ->addArgument($this->createVersionStrategy($name, $manifest))
        ;
    }

    protected function getPackageTag()
    {
        return $this->namespaceService('package.templating');
    }
}
