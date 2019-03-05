<?php

namespace CoRex\Composer\Repository\Commands;

use CoRex\Composer\Repository\Services\VersionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('test');
        $this->setDescription('Test');
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $versionService = VersionService::load();

    }
}