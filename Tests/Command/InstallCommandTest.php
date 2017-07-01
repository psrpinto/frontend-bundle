<?php

namespace Rj\FrontendBundle\Tests\Command;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Rj\FrontendBundle\Command\InstallCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class InstallCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var InstallCommand|PHPUnit_Framework_MockObject_MockObject
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp()
    {
        $fs = new Filesystem();
        $fs->remove($this->baseDir = sys_get_temp_dir().'/rj_frontend');
        mkdir($this->baseDir);

        $application = new Application();
        $application->add($command = $this->getCommand());

        $this->command = $application->find($command->getName());
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @runInSeparateProcess
     */
    public function testNpmNotInstalled()
    {
        $this->command->method('commandExists')
             ->will($this->returnCallback(function () {
                 $args = func_get_args();

                 return $args[0] !== 'npm';
             }));

        $this->commandTester->execute([]);
        $this->assertRegExp('/npm is not installed/', $this->commandTester->getDisplay());
    }

    /**
     * @runInSeparateProcess
     */
    public function testBowerNotInstalled()
    {
        $this->command->method('commandExists')
             ->will($this->returnCallback(function () {
                 $args = func_get_args();

                 return $args[0] !== 'bower';
             }));

        $this->commandTester->execute([]);
        $this->assertRegExp('/bower is not installed/', $this->commandTester->getDisplay());
    }

    /**
     * @runInSeparateProcess
     */
    public function testInstall()
    {
        $this->command->method('commandExists')->willReturn(true);

        $this->commandTester->execute([]);
        $this->assertRegExp('/Running `npm install`/', $this->commandTester->getDisplay());
        $this->assertRegExp('/Running `bower install`/', $this->commandTester->getDisplay());
    }

    /**
     * @return InstallCommand|PHPUnit_Framework_MockObject_MockObject
     */
    private function getCommand()
    {
        return $this->getMockBuilder('Rj\FrontendBundle\Command\InstallCommand')
            ->setMethods(['commandExists', 'runProcess'])
            ->getMock();
    }
}
