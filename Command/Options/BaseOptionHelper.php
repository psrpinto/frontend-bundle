<?php

namespace Rj\FrontendBundle\Command\Options;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseOptionHelper
{
    protected $defaultValue = 0;
    protected $allowedValues = [];
    protected $errorMessage = null;

    private $command;
    private $input;
    private $output;

    abstract protected function getQuestion($question);

    public function __construct(Command $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }

    public function setAllowedValues($allowedValues)
    {
        $this->allowedValues = $allowedValues;

        return $this;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function setOption($name, $question)
    {
        $selection = $this->input->getOption($name);
        $allowed = $this->allowedValues;
        $error = $this->errorMessage;

        if (null !== $selection && !empty($allowed) && !in_array($selection, $allowed)) {
            $this->output->writeln(sprintf("<error>$error</error>", $selection));
            $selection = null;
        }

        if (null === $selection) {
            $selection = $this->ask($this->getQuestion($question));
        }

        if ($selection === 'false') {
            $selection = false;
        }

        if ($selection === 'true') {
            $selection = true;
        }

        $this->input->setOption($name, $selection);

        return $selection;
    }

    private function ask($question)
    {
        return $this->command->getHelper('question')->ask($this->input, $this->output, $question);
    }
}
