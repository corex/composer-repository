<?php

namespace CoRex\Composer\Repository\Commands;

use CoRex\Composer\Repository\Browser;
use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Filesystem\Directory;
use CoRex\Filesystem\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('clear');
        $this->setDescription('Clear generated data');
        $this->setHelp('');
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

        $config = Config::load();
        if ($config->getPath() === null) {
            Console::throwError('Path not set. Run command "config:path" to set path (must be accessible via web).');
        }

        Console::warning('Warning! This will clear all data and data need to be rebuild.');
        if (Console::confirm('Are you sure', 'n', true)) {
            Directory::clean($config->getPath());
            File::delete($config->getPath(['.htaccess']));
            Browser::createBrowserFiles();
            Console::info('Data cleared.');
        } else {
            Console::info('Data not cleared.');
        }
    }
}