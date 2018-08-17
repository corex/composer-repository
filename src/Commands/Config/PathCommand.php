<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
use CoRex\Support\System\Directory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PathCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('config:path');
        $this->setDescription('Set path');
        $this->setDefinition([
            new InputArgument('path', InputArgument::REQUIRED, 'Path'),
        ]);
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Message::header($this->getDescription());

        $path = rtrim(realpath($input->getArgument('path')), '/');
        if (!Directory::exist($path)) {
            Message::error('Path ' . $path . ' does not exist.');
        }
        Config::load()->setPath($path)->save();

        Message::info('Path ' . $path . ' set.');
    }
}