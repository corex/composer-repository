<?php

namespace CoRex\Composer\Repository\Commands\Show;

use CoRex\Composer\Repository\Message;
use CoRex\Composer\Repository\Services\PackageService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangelogCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('show:changelog');
        $this->setDescription('Show changelog for package');
        $this->setDefinition([
            new InputArgument('signature', InputArgument::REQUIRED, 'Signature of package'),
            new InputArgument('version', InputArgument::REQUIRED, 'Version of package ("." or "-" for latest)')
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

        $signature = $input->getArgument('signature');
        $version = $input->getArgument('version');

        $package = PackageService::load($signature);
        if (in_array($version, ['*', '.', '-'])) {
            $version = $package->getLatestVersion();
        }
        if ($version === null) {
            Message::error('Package ' . $signature . ' not found.');
        }

        Message::info('Changelog for ' . $signature . ' ' . $version);
        Message::blank();

        $versionEntity = $package->getVersionEntity($version);
        if ($versionEntity === null) {
            Message::error('Version ' . $version . ' for package ' . $signature . ' not found.');
        }
        $content = $versionEntity->getMap()->getChangelogContent();
        print($content . "\n");
    }
}