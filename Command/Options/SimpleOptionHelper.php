<?php

namespace Rj\FrontendBundle\Command\Options;

use Rj\FrontendBundle\Util\Util;

class SimpleOptionHelper extends BaseOptionHelper
{
    public function getQuestion($question)
    {
        $class = Util::hasQuestionHelper()
            ? 'Symfony\Component\Console\Question\Question'
            : 'Rj\FrontendBundle\Command\Options\Legacy\Question'
        ;

        return new $class("<question>$question</question>", $this->defaultValue);
    }
}
