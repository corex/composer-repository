<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Helpers\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HomepageCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('config:homepage');
        $this->setDescription('Set homepage for repository');
        $this->setDefinition([
            new InputArgument('homepage', InputArgument::REQUIRED, 'Homepage of repository'),
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

        $homepage = $input->getArgument('homepage');
        if (empty($homepage)) {
            Console::throwError('Homepage not specified.');
        }
        $homepage = rtrim($homepage, '/');
        if (!Str::startsWith($homepage, 'http://') && !Str::startsWith($homepage, 'https://')) {
            Console::throwError('Homepage not valid.');
        }
        Config::load()->setHomepage($homepage)->save();

        Console::info('Homepage ' . $homepage . ' set.');
    }
}