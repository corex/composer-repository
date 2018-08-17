<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Helpers\Tabs;
use CoRex\Composer\Repository\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TabsCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $availableTabs = implode(', ', Tabs::available());
        $tabsArgumentDescription = 'Allowed tabs ("-" for all. Available: ' . $availableTabs . '.)';
        parent::configure();
        $this->setName('config:tabs');
        $this->setDescription('Set tabs for specified signature');
        $this->setDefinition([
            new InputArgument('signature', InputArgument::REQUIRED, 'Signature of package'),
            new InputArgument('tabs', InputArgument::REQUIRED, $tabsArgumentDescription)
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
        $tabs = $input->getArgument('tabs');

        if ($tabs !== '-') {
            $tabs = str_replace([' ', ';', '.', '/', ':'], ',', $tabs);
            $tabsArray = explode(',', $tabs);
        } else {
            $tabsArray = [];
        }

        Tabs::setSignature($signature, $tabsArray);

        if (count($tabsArray) > 0) {
            Message::info('Tabs ' . $tabs . ' set for ' . $signature . '.');
        } else {
            Message::info('All tabs set for ' . $signature . '.');
        }
    }
}