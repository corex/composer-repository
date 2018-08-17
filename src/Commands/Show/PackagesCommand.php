<?php

namespace CoRex\Composer\Repository\Commands\Show;

use CoRex\Composer\Repository\Message;
use CoRex\Composer\Repository\Services\PackagesService;
use CoRex\Support\System\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PackagesCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('show:packages');
        $this->setDescription('Show packages');
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

        $packages = PackagesService::load();
        $vendorNames = $packages->getVendorNames();
        if (count($vendorNames) > 0) {
            foreach ($vendorNames as $vendorName) {
                $packageNames = $packages->getPackageNames($vendorName);
                Console::write($vendorName . ' : ');
                Console::words($packageNames);
                Console::writeln('');
            }
        } else {
            Message::info('No vendors found.');
        }
    }
}