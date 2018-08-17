<?php

namespace CoRex\Composer\Repository\Commands\Show;

use CoRex\Composer\Repository\Message;
use CoRex\Composer\Repository\Services\PackagesService;
use CoRex\Support\System\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VendorsCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('show:vendors');
        $this->setDescription('Show vendors');
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

        $vendorNames = PackagesService::load()->getVendorNames();
        if (count($vendorNames) > 0) {
            Console::words($vendorNames);
            Console::writeln('');
        } else {
            Message::info('No vendors found.');
        }
    }
}