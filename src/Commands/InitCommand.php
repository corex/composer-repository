<?php

namespace CoRex\Composer\Repository\Commands;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Path;
use CoRex\Filesystem\Directory;
use CoRex\Helpers\Str;
use CoRex\Terminal\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('init');
        $this->setDescription('Initialize');
        $this->setHelp('');
        $this->setDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'Name/Title of repository.'),
            new InputArgument('package-name', InputArgument::REQUIRED,
                'Package-name of repository i.e. "corex/composer-repository".'),
            new InputArgument('homepage', InputArgument::REQUIRED, 'Homepage of repository (url).')
        ]);
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Console::header($this->getDescription());

        $name = $input->getArgument('name');
        $packageName = $input->getArgument('package-name');
        $homepage = rtrim($input->getArgument('homepage'), '/');

        // Make sure config directory exists.
        $configPath = Path::root(['config']);
        if (!Directory::exist($configPath)) {
            Directory::make($configPath);
        }

        $config = Config::load();
        if ($config->exist()) {
            Console::info($config->getName() . ' already initialized.');
            return;
        }
        $config->setName($name);
        $config->setPackageName($packageName);
        $config->setHomepage($homepage);
        $config->save();

        if (!Str::startsWith($homepage, 'https://')) {
            Console::warning('Warning: homepage specified is not secure (https).');
            Console::br();
        }

        Console::info($config->getName() . ' initialized.');
    }
}