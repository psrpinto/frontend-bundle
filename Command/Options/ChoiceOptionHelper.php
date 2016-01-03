<?php

namespace Rj\FrontendBundle\Command\Options;

use Rj\FrontendBundle\Util\Util;

class ChoiceOptionHelper extends BaseOptionHelper
{
    public function getQuestion($question)
    {
        $class = Util::hasQuestionHelper()
            ? 'Symfony\Component\Console\Question\ChoiceQuestion'
            : 'Rj\FrontendBundle\Command\Options\Legacy\ChoiceQuestion'
        ;

        $question = new $class("<question>$question</question>", $this->allowedValues, $this->defaultValue);
        $question->setErrorMessage($this->errorMessage);

        return $question;
    }
}
