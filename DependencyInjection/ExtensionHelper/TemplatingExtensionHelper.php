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

    public function getPackageTag()
    {
        return $this->namespaceService('package.templating');
    }

    protected function getPackageDefinition($isUrl)
    {
        return $isUrl
            ? new DefinitionDecorator($this->namespaceService('templating.package.url'))
            : new DefinitionDecorator($this->namespaceService('templating.package.path'))
        ;
    }

    protected function getFallbackPackageDefinition()
    {
        return new DefinitionDecorator($this->namespaceService('templating.package.fallback'));
    }
}
