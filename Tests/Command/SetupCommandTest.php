<?php

namespace Rj\FrontendBundle\Tests\Command;

use Rj\FrontendBundle\Command\SetupCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class SetupCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $fs = new Filesystem();
        $fs->remove($this->baseDir = sys_get_temp_dir().'/rj_frontend');
        mkdir($this->baseDir);

        $application = new Application();

        $application->add($this->getInstallCommand());
        $application->add($command = new SetupCommand());

        $this->command = $application->find($command->getName());
        $this->command->setRootDir($this->baseDir);

        $this->commandTester = new CommandTester($this->command);
    }

    public function testDefaultOptions()
    {
        $this->assertOptions(array('src-dir' => null), array('src-dir' => $this->baseDir.'/app/Resources'));
        $this->assertOptions(array('dest-dir' => null), array('dest-dir' => $this->baseDir.'/web/assets'));
        $this->assertOptions(array('pipeline' => null), array('pipeline' => 'gulp'));
        $this->assertOptions(array('csspre' => null), array('csspre' => 'sass'));
        $this->assertOptions(array('coffee' => null), array('coffee' => false));
    }

    public function testOptions()
    {
        $this->assertOptions(array('src-dir' => 'foo'), array('src-dir' => 'foo'));
        $this->assertOptions(array('dest-dir' => 'web/foo'), array('dest-dir' => 'web/foo'));
        $this->assertOptions(array('pipeline' => 'gulp'), array('pipeline' => 'gulp'));
        $this->assertOptions(array('csspre' => 'less'), array('csspre' => 'less'));
        $this->assertOptions(array('coffee' => true), array('coffee' => true));
    }

    public function testOptionsNoInteraction()
    {
        $this->assertOptions(array(), array(
            'src-dir' => $this->baseDir.'/app/Resources',
            'dest-dir' => $this->baseDir.'/web/assets',
            'pipeline' => 'gulp',
            'csspre' => 'sass',
            'coffee' => false,
        ), false);

        $this->assertOptions(array('src-dir' => 'foo'), array('src-dir' => 'foo', 'dest-dir' => $this->baseDir.'/web/assets'), false);
        $this->assertOptions(array('dest-dir' => 'web/foo'), array('src-dir' => $this->baseDir.'/app/Resources', 'dest-dir' => 'web/foo'), false);
        $this->assertOptions(array('pipeline' => 'gulp'), array('src-dir' => $this->baseDir.'/app/Resources', 'pipeline' => 'gulp'), false);
        $this->assertOptions(array('csspre' => 'less'), array('src-dir' => $this->baseDir.'/app/Resources', 'csspre' => 'less'), false);
        $this->assertOptions(array('coffee' => 'true'), array('src-dir' => $this->baseDir.'/app/Resources', 'coffee' => true), false);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage 'dest-dir' must be a directory under web/
     */
    public function testInvalidDestDir()
    {
        $this->commandTester->execute(array('--dest-dir' => 'foo'), array('interactive' => false));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage 'dest-dir' must be a directory under web/
     */
    public function testInvalidDestDirWebRoot()
    {
        $this->commandTester->execute(array('--dest-dir' => 'web'), array('interactive' => false));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage 'dest-dir' must be a directory under web/
     */
    public function testInvalidDestDirWebRoot2()
    {
        $this->commandTester->execute(array('--dest-dir' => 'web/'), array('interactive' => false));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSourceTreeDefault()
    {
        $base = $this->baseDir;

        $this->commandTester->execute(
            array('--src-dir' => $base),
            array('interactive' => false)
        );

        $this->assertFileExists("$base/images/.keep");
        $this->assertFileExists("$base/scripts/app.js");
        $this->assertFileExists("$base/stylesheets/app.scss");
        $this->assertFileExists("$base/stylesheets/vendor.scss");

        $this->assertFileNotExists("$base/stylesheets/app.less");
        $this->assertFileNotExists("$base/scripts/app.coffee");
    }

    /**
     * @runInSeparateProcess
     */
    public function testSourceTreeLess()
    {
        $base = $this->baseDir;

        $this->commandTester->execute(
            array(
                '--src-dir' => $base,
                '--csspre' => 'less',
            ),
            array('interactive' => false)
        );

        $this->assertFileExists("$base/stylesheets/app.less");
        $this->assertFileExists("$base/stylesheets/vendor.less");

        $this->assertFileNotExists("$base/stylesheets/app.scss");
    }

    /**
     * @runInSeparateProcess
     */
    public function testSourceTreeCoffee()
    {
        $base = $this->baseDir;

        $this->commandTester->execute(
            array(
                '--src-dir' => $base,
                '--coffee' => 'true',
            ),
            array('interactive' => false)
        );

        $this->assertFileExists("$base/scripts/app.coffee");

        $this->assertFileNotExists("$base/scripts/app.js");
    }

    /**
     * @runInSeparateProcess
     */
    public function testSourceTreeDryRun()
    {
        $base = $this->baseDir;

        $this->commandTester->execute(array(
            '--src-dir' => $base,
            '--dry-run' => true,
        ), array('interactive' => false));

        $this->assertFileNotExists("$base/images/.keep");
        $this->assertFileNotExists("$base/scripts/.keep");
        $this->assertFileNotExists("$base/stylesheets/.keep");
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateFilesDryRun()
    {
        $base = $this->baseDir;

        $this->commandTester->execute(array(
            '--dry-run' => true,
        ), array('interactive' => false));

        $this->assertRegExp("|Would have created file $base/gulpfile.js|", $this->commandTester->getDisplay());
        $this->assertRegExp("|Would have created file $base/package.json|", $this->commandTester->getDisplay());
        $this->assertRegExp("|Would have created file $base/bower.json|", $this->commandTester->getDisplay());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateFilesExists()
    {
        $base = $this->baseDir;

        touch($base.'/gulpfile.js');
        touch($base.'/package.json');
        touch($base.'/bower.json');

        $this->commandTester->execute(array(), array('interactive' => false));

        $this->assertRegExp("|$base/gulpfile.js already exists|", $this->commandTester->getDisplay());
        $this->assertRegExp("|$base/package.json already exists|", $this->commandTester->getDisplay());
        $this->assertRegExp("|$base/bower.json already exists|", $this->commandTester->getDisplay());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateFilesExistsForce()
    {
        $base = $this->baseDir;

        touch($base.'/gulpfile.js');
        touch($base.'/package.json');
        touch($base.'/bower.json');

        $this->commandTester->execute(array(
            '--force' => true,
        ), array('interactive' => false));

        $this->assertRegExp("|Creating file $base/gulpfile.js|", $this->commandTester->getDisplay());
        $this->assertRegExp("|Creating file $base/package.json|", $this->commandTester->getDisplay());
        $this->assertRegExp("|Creating file $base/bower.json|", $this->commandTester->getDisplay());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateFiles()
    {
        $base = $this->baseDir;

        $this->commandTester->execute(array(), array('interactive' => false));

        $this->assertRegExp("|Creating file $base/gulpfile.js|", $this->commandTester->getDisplay());
        $this->assertNotEmpty(file_get_contents("$base/gulpfile.js"));

        $this->assertRegExp("|Creating file $base/package.json|", $this->commandTester->getDisplay());
        $this->assertNotEmpty(file_get_contents("$base/package.json"));

        $this->assertRegExp("|Creating file $base/bower.json|", $this->commandTester->getDisplay());
        $this->assertNotEmpty(file_get_contents("$base/bower.json"));
    }

    /**
     * @runInSeparateProcess
     */
    public function testInstallDependenciesDryRun()
    {
        $this->commandTester->execute(array(
            '--dry-run' => true,
        ), array('interactive' => false));

        $this->assertRegExp('/Would have installed npm and bower dependencies/', $this->commandTester->getDisplay());
    }

    private function assertOptions($options, $expected, $interactive = true)
    {
        $defaults = !$interactive ? array() : array(
            'src-dir' => 'bar',
            'dest-dir' => 'web/bar',
            'pipeline' => 'bar',
            'csspre' => 'bar',
            'coffee' => 'bar',
        );

        $options = array_merge($defaults, $options);
        $options['dry-run'] = true;

        foreach ($options as $key => $value) {
            if ($value !== null) {
                $options["--$key"] = $value;
            }

            unset($options[$key]);
        }

        // Simulate the user pressing enter. This is needed to test the default
        // value, has no impact when the value is provided.
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream(PHP_EOL));

        $this->commandTester->execute($options, array(
            'interactive' => $interactive,
        ));

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $this->commandTester->getInput()->getOption($key));
        }
    }

    private function getInstallCommand()
    {
        return $this->getMockBuilder('Rj\FrontendBundle\Command\InstallCommand')
            ->setMethods(array('commandExists', 'runProcess'))
            ->getMock()
        ;
    }

    private function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
