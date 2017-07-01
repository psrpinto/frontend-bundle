<?php

namespace Rj\FrontendBundle\Command;

use Rj\FrontendBundle\Command\Options\SimpleOptionHelper;
use Rj\FrontendBundle\Command\Options\ChoiceOptionHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;

class SetupCommand extends Command
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string|null
     */
    private $rootDir = null;

    /**
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->templating = new PhpEngine(
            new TemplateNameParser(),
            new FilesystemLoader([__DIR__.'/../Resources/blueprints/%name%'])
        );
    }

    /**
     * @param string $path
     */
    public function setRootDir($path)
    {
        $this->rootDir = $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rj_frontend:setup')
            ->setDescription('Generate the configuration for the asset pipeline')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Output which commands would have been run instead of running them'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force execution'
            )
            ->addOption(
                'src-dir',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to the directory containing the source assets [e.g. '.$this->getDefaultOption('src-dir').']'
            )
            ->addOption(
                'dest-dir',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to the directory containing the compiled assets [e.g. '.$this->getDefaultOption('dest-dir').']'
            )
            ->addOption(
                'pipeline',
                null,
                InputOption::VALUE_REQUIRED,
                'Asset pipeline to use [only gulp is available at the moment]'
            )
            ->addOption(
                'csspre',
                null,
                InputOption::VALUE_REQUIRED,
                'CSS preprocessor to use [sass, less or none]'
            )
            ->addOption(
                'coffee',
                null,
                InputOption::VALUE_REQUIRED,
                'Use the CoffeeScript compiler [true or false]'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $simpleOptionHelper = new SimpleOptionHelper($this, $input, $output);
        $choiceOptionHelper = new ChoiceOptionHelper($this, $input, $output);

        $simpleOptionHelper
            ->setDefaultValue($this->getDefaultOption('src-dir'))
            ->setOption(
                'src-dir',
                'Path to the directory containing the source assets [default is '.$this->getDefaultOption('src-dir').']'
            )
        ;

        $simpleOptionHelper
            ->setDefaultValue($this->getDefaultOption('dest-dir'))
            ->setOption(
                'dest-dir',
                'Path to the directory containing the compiled assets [default is '.$this->getDefaultOption('dest-dir').']'
            )
        ;

        $choiceOptionHelper
            ->setAllowedValues(['gulp'])
            ->setErrorMessage('%s is not a supported asset pipeline')
            ->setOption(
                'pipeline',
                'Asset pipeline to use [only gulp is available at the moment]'
            )
        ;

        $choiceOptionHelper
            ->setAllowedValues(['sass', 'less', 'none'])
            ->setErrorMessage('%s is not a supported CSS preprocessor')
            ->setOption(
                'csspre',
                'CSS preprocessor to use [default is '.$this->getDefaultOption('csspre').']'
            )
        ;

        $choiceOptionHelper
            ->setAllowedValues(['false', 'true'])
            ->setErrorMessage('%s is not a supported value for --coffee. Use either true or false')
            ->setOption(
                'coffee',
                'Whether to use the CoffeeScript compiler [default is '.$this->getDefaultOption('coffee').']'
            )
        ;

        $output->writeln('');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processOptions($input);

        $output->writeln('<info>Selected options are:</info>');
        $output->writeln('src-dir:  '.$input->getOption('src-dir'));
        $output->writeln('dest-dir: '.$input->getOption('dest-dir'));
        $output->writeln('pipeline: '.$input->getOption('pipeline'));
        $output->writeln('csspre:   '.$input->getOption('csspre'));
        $output->writeln('coffee:   '.($input->getOption('coffee') ? 'true' : 'false'));

        if (!preg_match('|web/.+|', $input->getOption('dest-dir'))) {
            throw new \InvalidArgumentException("'dest-dir' must be a directory under web/");
        }

        $output->writeln('');
        $this->createSourceTree($input, $output);
        $this->createBuildFile($input, $output);
        $this->createPackageJson($input, $output);
        $this->createBowerJson($input, $output);

        $output->writeln('');
        $this->runInstallCommand($input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function runInstallCommand(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dry-run')) {
            return $output->writeln('<info>Would have installed npm and bower dependencies</info>');
        }

        $this->getApplication()->find('rj_frontend:install')
            ->run(new ArrayInput(['command' => 'rj_frontend:install']), $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function createSourceTree(InputInterface $input, OutputInterface $output)
    {
        $blueprints = __DIR__.'/../Resources/blueprints';
        $dryRun = $input->getOption('dry-run');
        $base = $input->getOption('src-dir');

        $output->writeln($dryRun
            ? '<info>Would have created directory tree for source assets:</info>'
            : '<info>Creating directory tree for source assets:</info>'
        );

        $blueprintDir = "$blueprints/images";
        $this->createDirFromBlueprint($input, $output, $blueprintDir, "$base/images");

        $blueprintDir = "$blueprints/stylesheets/".$input->getOption('csspre');
        $this->createDirFromBlueprint($input, $output, $blueprintDir, "$base/stylesheets");

        $blueprintDir = "$blueprints/scripts/";
        $blueprintDir .= $input->getOption('coffee') ? 'coffee' : 'js';
        $this->createDirFromBlueprint($input, $output, $blueprintDir, "$base/scripts");

        $output->writeln('');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function createBuildFile(InputInterface $input, OutputInterface $output)
    {
        $files = [
            'gulp' => 'gulp/gulpfile.js',
        ];

        $this->createFileFromTemplate($input, $output, 'pipelines/'.$files[$input->getOption('pipeline')]);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function createPackageJson(InputInterface $input, OutputInterface $output)
    {
        $files = [
            'gulp' => 'gulp/package.json',
        ];

        $this->createFileFromTemplate($input, $output, 'pipelines/'.$files[$input->getOption('pipeline')]);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function createBowerJson(InputInterface $input, OutputInterface $output)
    {
        $this->createFileFromTemplate($input, $output, 'bower.json');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $blueprintDir
     * @param string          $targetDir
     */
    private function createDirFromBlueprint(InputInterface $input, OutputInterface $output, $blueprintDir, $targetDir)
    {
        $dryRun = $input->getOption('dry-run');

        if (!$dryRun && !file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        foreach (preg_grep('/^\.?\w+/', scandir($blueprintDir)) as $entry) {
            $target = $entry;

            $isPhpTemplate = substr($entry, strrpos($entry, '.')) === '.php';
            if ($isPhpTemplate) {
                $entry = str_replace('.php', '', $entry);
                $target = str_replace('.php', '', $target);
            }

            $entry = $blueprintDir.'/'.$entry;
            $target = $targetDir.'/'.$target;

            if (!$dryRun) {
                if ($isPhpTemplate) {
                    $this->renderTemplate($input, $output, $entry, $target);
                } else {
                    if (file_exists($target) && !$input->getOption('force')) {
                        $output->writeln(
                            "<error>$target already exists. Run this command with --force to overwrite</error>
                        ");

                        continue;
                    }

                    copy($entry, $target);
                }
            }

            $output->writeln($target);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $file
     */
    private function createFileFromTemplate(InputInterface $input, OutputInterface $output, $file)
    {
        $dryRun = $input->getOption('dry-run');

        $targetFile = basename($file);
        if (!empty($this->rootDir)) {
            $targetFile = $this->rootDir.'/'.$targetFile;
        }

        $output->writeln($dryRun
            ? "<info>Would have created file $targetFile</info>"
            : "<info>Creating file $targetFile</info>"
        );

        if ($dryRun) {
            return;
        }

        $this->renderTemplate($input, $output, $file, $targetFile);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $file
     * @param string          $target
     */
    private function renderTemplate(InputInterface $input, OutputInterface $output, $file, $target)
    {
        if (file_exists($target) && !$input->getOption('force')) {
            $output->writeln(
                "<error>$target already exists. Run this command with --force to overwrite</error>"
            );
        }

        switch ($input->getOption('csspre')) {
            case 'sass':
                $stylesheetExtension = 'scss';
                break;
            case 'less':
                $stylesheetExtension = 'less';
                break;
            default:
                $stylesheetExtension = 'css';
                break;
        }

        file_put_contents($target, $this->templating->render("$file.php", [
            'projectName' => basename(getcwd()),
            'srcDir' => $input->getOption('src-dir'),
            'destDir' => $input->getOption('dest-dir'),
            'prefix' => str_replace('web/', '', $input->getOption('dest-dir')),
            'coffee' => $input->getOption('coffee'),
            'cssPre' => $input->getOption('csspre'),
            'stylesheetExtension' => $stylesheetExtension,
        ]));
    }

    /**
     * @param InputInterface $input
     */
    private function processOptions(InputInterface $input)
    {
        foreach ($input->getOptions() as $name => $value) {
            if (!$input->isInteractive() && $value === null) {
                $value = $this->getDefaultOption($name);
            }

            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }

            $input->setOption($name, $value);
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getDefaultOption($name)
    {
        $defaults = [
            'src-dir' => empty($this->rootDir) ? 'app/Resources' : $this->rootDir.'/app/Resources',
            'dest-dir' => empty($this->rootDir) ? 'web/assets' : $this->rootDir.'/web/assets',
            'pipeline' => 'gulp',
            'csspre' => 'sass',
            'coffee' => 'false',
        ];

        return $defaults[$name];
    }
}
