<?php

namespace Rj\FrontendBundle\Tests\Command;

use Rj\FrontendBundle\Command\SetupCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class SetupCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var SetupCommand
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

        $application->add($command = new SetupCommand());

        $this->command = $application->find($command->getName());
        $this->command->setRootDir($this->baseDir);

        $this->commandTester = new CommandTester($this->command);
    }

    public function testDefaultOptions()
    {
        $this->assertOptions(['src-dir' => null], ['src-dir' => $this->baseDir.'/app/Resources']);
        $this->assertOptions(['dest-dir' => null], ['dest-dir' => $this->baseDir.'/web/assets']);
        $this->assertOptions(['pipeline' => null], ['pipeline' => 'gulp']);
        $this->assertOptions(['csspre' => null], ['csspre' => 'sass']);
        $this->assertOptions(['coffee' => null], ['coffee' => false]);
    }

    public function testOptions()
    {
        $this->assertOptions(['src-dir' => 'foo'], ['src-dir' => 'foo']);
        $this->assertOptions(['dest-dir' => 'web/foo'], ['dest-dir' => 'web/foo']);
        $this->assertOptions(['pipeline' => 'gulp'], ['pipeline' => 'gulp']);
        $this->assertOptions(['csspre' => 'less'], ['csspre' => 'less']);
        $this->assertOptions(['coffee' => true], ['coffee' => true]);
    }

    public function testOptionsNoInteraction()
    {
        $this->assertOptions([], [
            'src-dir' => $this->baseDir.'/app/Resources',
            'dest-dir' => $this->baseDir.'/web/assets',
            'pipeline' => 'gulp',
            'csspre' => 'sass',
            'coffee' => false,
        ], false);

        $this->assertOptions(['src-dir' => 'foo'], ['src-dir' => 'foo', 'dest-dir' => $this->baseDir.'/web/assets'], false);
        $this->assertOptions(['dest-dir' => 'web/foo'], ['src-dir' => $this->baseDir.'/app/Resources', 'dest-dir' => 'web/foo'], false);
        $this->assertOptions(['pipeline' => 'gulp'], ['src-dir' => $this->baseDir.'/app/Resources', 'pipeline' => 'gulp'], false);
        $this->assertOptions(['csspre' => 'less'], ['src-dir' => $this->baseDir.'/app/Resources', 'csspre' => 'less'], false);
        $this->assertOptions(['coffee' => 'true'], ['src-dir' => $this->baseDir.'/app/Resources', 'coffee' => true], false);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage 'dest-dir' must be a directory under web/
     */
    public function testInvalidDestDir()
    {
        $this->commandTester->execute(['--dest-dir' => 'foo'], ['interactive' => false]);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage 'dest-dir' must be a directory under web/
     */
    public function testInvalidDestDirWebRoot()
    {
        $this->commandTester->execute(['--dest-dir' => 'web'], ['interactive' => false]);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage 'dest-dir' must be a directory under web/
     */
    public function testInvalidDestDirWebRoot2()
    {
        $this->commandTester->execute(['--dest-dir' => 'web/'], ['interactive' => false]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSourceTreeDefault()
    {
        $base = $this->baseDir;

        $this->commandTester->execute(
            ['--src-dir' => $base],
            ['interactive' => false]
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
            [
                '--src-dir' => $base,
                '--csspre' => 'less',
            ],
            ['interactive' => false]
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
            [
                '--src-dir' => $base,
                '--coffee' => 'true',
            ],
            ['interactive' => false]
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

        $this->commandTester->execute([
            '--src-dir' => $base,
            '--dry-run' => true,
        ], ['interactive' => false]);

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

        $this->commandTester->execute([
            '--dry-run' => true,
        ], ['interactive' => false]);

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

        $this->commandTester->execute([], ['interactive' => false]);

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

        $this->commandTester->execute([
            '--force' => true,
        ], ['interactive' => false]);

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

        $this->commandTester->execute([], ['interactive' => false]);

        $this->assertRegExp("|Creating file $base/gulpfile.js|", $this->commandTester->getDisplay());
        $this->assertNotEmpty(file_get_contents("$base/gulpfile.js"));

        $this->assertRegExp("|Creating file $base/package.json|", $this->commandTester->getDisplay());
        $this->assertNotEmpty(file_get_contents("$base/package.json"));

        $this->assertRegExp("|Creating file $base/bower.json|", $this->commandTester->getDisplay());
        $this->assertNotEmpty(file_get_contents("$base/bower.json"));
    }

    /**
     * @param array $options
     * @param array $expected
     * @param bool  $interactive
     */
    private function assertOptions(array $options, array $expected, $interactive = true)
    {
        $defaults = !$interactive ? [] : [
            'src-dir' => 'bar',
            'dest-dir' => 'web/bar',
            'pipeline' => 'bar',
            'csspre' => 'bar',
            'coffee' => 'bar',
        ];

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
        $this->commandTester->setInputs([PHP_EOL]);

        $this->commandTester->execute($options, [
            'interactive' => $interactive,
        ]);

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $this->commandTester->getInput()->getOption($key));
        }
    }
}
