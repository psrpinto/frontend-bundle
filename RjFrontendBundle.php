<?php

namespace Rj\FrontendBundle;

use Rj\FrontendBundle\Util\Util;
use Rj\FrontendBundle\DependencyInjection\Compiler\Packages\AssetCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;
use Symfony\Bundle\FrameworkBundle\Console\Application as FrameworkApplication;

class RjFrontendBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AssetCompilerPass());
    }

    public function registerCommands(Application $app)
    {
        if ($app instanceof FrameworkApplication) {
            $this->addConsoleHelpers($app);
        }

        return parent::registerCommands($app);
    }

    private function addConsoleHelpers(FrameworkApplication $app)
    {
        if (!Util::hasQuestionHelper()) {
            $helper = $app->getKernel()->getContainer()->get('rj_frontend.console.helper.question_legacy');
            $app->getHelperSet()->set($helper, 'question');
        }
    }
}
