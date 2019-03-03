<?php

namespace CoRex\Composer\Repository\Commands;

use CoRex\Composer\Repository\Helpers\Build;
use CoRex\Composer\Repository\Helpers\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('order');
        $this->setDescription('Order build');
        $this->setHelp('');
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
        Console::header($this->getDescription());
        Build::order();
        Console::info('Build ordered.');
    }
}