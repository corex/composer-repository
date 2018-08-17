<?php

namespace CoRex\Composer\Repository\Commands\Package;

use Composer\Command\BaseCommand;
use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
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
        Message::header($this->getDescription());

        $signature = $input->getArgument('signature');

        // Remove package.
        Message::info('Removing package ' . $signature);
        Message::blank();
        $config = Config::load();
        $isRemoved = $config->removePackage($signature);
        if ($isRemoved) {
            $config->save();
            Message::info('Package ' . $signature . ' removed.');
        } else {
            Message::error('Package ' . $signature . ' not found.');
        }
    }
}