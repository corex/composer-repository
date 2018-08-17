<?php

namespace CoRex\Composer\Repository\Commands\Package;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
use CoRex\Composer\Repository\Services\PackagistService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddNameCommand extends Command
{
    private $definition;

    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->definition = $this->getDefinition();
        $this->setName('package:add-signature');
        $this->setDescription('Add package by name (searches packagist)');
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
        Message::header($this->getDescription());

        $signature = $input->getArgument('signature');

        Message::info('Adding package ' . $signature);

        if (!PackagistService::packagistHasPackage($signature)) {
            Message::error('Package ' . $signature . ' not found.');
        }

        // Add package.
        Message::blank();
        $config = Config::load();
        $isAdded = $config->addPackageSignature($signature);
        if ($isAdded) {
            $config->save();
            Message::info('Package added.');
        } else {
            Message::error('Package already added.');
        }
    }
}