<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NameCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('config:name');
        $this->setDescription('Set repository name');
        $this->setDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'Name of repository'),
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

        $name = $input->getArgument('name');
        if (empty($name)) {
            Message::error('Name not specified.');
        }
        Config::load()->setName($name)->save();

        Message::info('Name ' . $name . ' set.');
    }
}