<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Helpers\Is;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailFromCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('config:email-from');
        $this->setDescription('Add notification email from address');
        $this->setDefinition([
            new InputArgument('email', InputArgument::REQUIRED, 'Email from address')
        ]);
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');

        // Validate email-address.
        if (!Is::email($email)) {
            Console::throwError('Email address ' . $email . ' not valid.');
        }

        $config = Config::load();
        $config->setEmailFrom($email);
        $config->save();
        Console::info('Email from-address ' . $email . ' set.');
    }
}