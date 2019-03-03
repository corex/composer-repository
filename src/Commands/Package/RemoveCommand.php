<?php

namespace CoRex\Composer\Repository\Commands\Package;

use Composer\Command\BaseCommand;
use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Build;
use CoRex\Composer\Repository\Helpers\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends BaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('package:remove');
        $this->setDescription('Remove package');
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

        // Remove package.
        Console::info('Removing package ' . $signature);
        Console::br();
        $config = Config::load();

        $isRemoved = $config->removePackage($signature);
        if ($isRemoved) {
            $config->save();
            Console::info('Package ' . $signature . ' removed.');
            Build::order();
            Console::info('Build ordered.');
        } else {
            Console::error('Package ' . $signature . ' not found.');
        }
    }
}