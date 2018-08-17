<?php

namespace CoRex\Composer\Repository\Commands;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
use CoRex\Helpers\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('init');
        $this->setDescription('Initialize');
        $this->setHelp('');
        $this->setDefinition([
            new InputArgument('name', InputArgument::REQUIRED, 'Name of repository.'),
            new InputArgument('homepage', InputArgument::REQUIRED, 'Homepage of repository.')
        ]);
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Message::header($this->getDescription());

        $name = $input->getArgument('name');
        $homepage = rtrim($input->getArgument('homepage'), '/');

        $config = Config::load();
        if ($config->exist()) {
            Message::info($config->getName() . ' already initialized.');
            return 1;
        }
        $config->setName($name);
        $config->setHomepage($homepage);
        $config->save();

        if (!Str::startsWith($homepage, 'https://')) {
            Message::warning('Warning: homepage specified is not secure (https).');
            Message::blank();
        }

        Message::info($config->getName() . ' initialized.');
    }
}