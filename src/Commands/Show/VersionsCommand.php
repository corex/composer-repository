<?php

namespace CoRex\Composer\Repository\Commands\Show;

use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Services\PackageService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VersionsCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('show:versions');
        $this->setDescription('Show versions');
        $this->setDefinition([
            new InputArgument('signature', InputArgument::REQUIRED, 'Signature of package')
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
        Console::header($this->getDescription());

        $signature = $input->getArgument('signature');

        $package = PackageService::load($signature);
        $versions = $package->getVersions();
        if (count($versions) > 0) {
            Console::words($versions);
            Console::writeln('');
            Console::writeln('');
            Console::writeln('Latest version : ' . $package->getLatestVersion());
        } else {
            Console::info('Package ' . $signature . ' not found.');
        }
    }
}