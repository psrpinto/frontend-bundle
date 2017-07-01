<?php

namespace Rj\FrontendBundle\Command\Options;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class BaseOptionHelper
{
    /**
     * @var mixed
     */
    protected $defaultValue = 0;

    /**
     * @var array
     */
    protected $allowedValues = [];

    /**
     * @var string|null
     */
    protected $errorMessage = null;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param string $question
     *
     * @return Question
     */
    abstract protected function getQuestion($question);

    /**
     * @param Command         $command
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(Command $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param array $allowedValues
     *
     * @return $this
     */
    public function setAllowedValues(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;

        return $this;
    }

    /**
     * @param string $errorMessage
     *
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @param string $name
     * @param string $question
     *
     * @return string|bool
     */
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

    /**
     * @param Question $question
     *
     * @return mixed
     */
    private function ask(Question $question)
    {
        return $this->command->getHelper('question')->ask($this->input, $this->output, $question);
    }
}
