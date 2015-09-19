<?php

namespace Rj\FrontendBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('rj_frontend:install')
            ->setDescription('Install npm and bower dependencies')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->commandExists('npm')) {
            return $output->writeln(
'<error>npm is not installed</error>

node.js is probably not installed on your system. For node.js installation
instructions, refer to https://nodejs.org/en/download/package-manager
'
            );
        }

        if (!$this->commandExists('bower')) {
            return $output->writeln(
'<error>bower is not installed</error>

You can install bower using npm:
npm install -g bower
'
            );
        }

        $output->writeln('<info>Running `npm install`</info>');
        $this->runProcess($output, 'npm install');

        $output->writeln('<info>Running `bower install`</info>');
        $this->runProcess($output, 'bower install');
    }

    protected function runProcess($output, $command)
    {
        $process = new Process($command);

        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->writeln("<error>$buffer</error>");
            } else {
                $output->writeln($buffer);
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    protected function commandExists($command)
    {
        $process = new Process("$command -v");
        $process->run();

        if (!$process->isSuccessful()) {
            return !preg_match('/: not found/', $process->getErrorOutput());
        }

        return true;
    }
}
