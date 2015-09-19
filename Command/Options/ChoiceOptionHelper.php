<?php

namespace Rj\FrontendBundle\Command\Options;

use Symfony\Component\Console\Question\ChoiceQuestion;

class ChoiceOptionHelper extends BaseOptionHelper
{
    public function getQuestion($question)
    {
        $question = new ChoiceQuestion("<question>$question</question>", $this->allowedValues, $this->defaultValue);
        $question->setErrorMessage($this->errorMessage);

        return $question;
    }
}
