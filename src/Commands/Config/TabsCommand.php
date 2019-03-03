<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Exceptions\SignatureException;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Helpers\Signature;
use CoRex\Composer\Repository\Helpers\Tabs;
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
        Console::header($this->getDescription());

        $signature = $input->getArgument('signature');

        // Check signature if valid.
        if (!Signature::isValid($signature)) {
            throw new SignatureException('Signature ' . $signature . ' not valid.');
        }

        $tabs = $input->getArgument('tabs');

        if ($tabs !== '-') {
            $tabs = str_replace([' ', ';', '.', '/', ':'], ',', $tabs);
            $tabsArray = explode(',', $tabs);
        } else {
            $tabsArray = [];
        }

        // Validate tabs.
        $availableTabs = Tabs::available();
        foreach ($tabsArray as $tab) {
            if (!in_array($tab, $availableTabs)) {
                Console::throwError('Tab ' . $tab . ' not valid.');
            }
        }

        Tabs::setSignature($signature, $tabsArray);

        if (count($tabsArray) > 0) {
            Console::info('Tabs ' . $tabs . ' set for ' . $signature . '.');
        } else {
            Console::info('All tabs set for ' . $signature . '.');
        }
    }
}