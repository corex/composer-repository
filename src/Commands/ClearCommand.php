<?php

namespace CoRex\Composer\Repository\Commands;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
use CoRex\Filesystem\Directory;
use CoRex\Filesystem\File;
use CoRex\Support\System\Console;
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
        $this->setDescription('Clear all data');
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
        Message::header($this->getDescription());

        Message::warning('Warning! This will clear all data and they need to be rebuild.');
        if (Console::confirm('Are you sure', true, false)) {
            Directory::clean(Config::load()->getPath());
            File::delete(Config::load()->getPath(['.htaccess']));
            Message::info('Data cleared.');
        } else {
            Message::info('Data not cleared.');
        }
    }
}