<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Constants;
use CoRex\Composer\Repository\Helpers\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('config:show');
        $this->setDescription('Show configuration');
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

        $config = Config::load();
        Console::properties([
            'Name' => $config->getName(),
            'Package' => $config->getPackageName(),
            'Homepage' => $config->getHomepage(),
            'Path' => $config->getPath(),
            'Email (from)' => $config->getEmailFrom(),
            'Email (to)' => implode(', ', $config->getEmailTos())
        ]);

        // Packages.
        $packages = $config->getPackages();
        $result = [];
        foreach ($packages as $signature => $details) {
            if ($details === null) {
                $details = Constants::PACKAGIST_URL;
            }
            $result[] = [
                'signature' => $signature,
                'source' => $details
            ];
        }
        sort($result);

        Console::br();
        if (count($result) > 0) {
            Console::info('Packages');
            Console::table($result, ['Package signature', 'Repository url']);
        } else {
            Console::info('No packages registered.');
        }

        // Tabs.
        $tabSignatures = $config->getTabSignatures();
        $result = [];
        foreach ($tabSignatures as $tabSignature) {
            $allowedTabs = $config->getSignatureTabs($tabSignature);
            $result[] = [
                'signature' => $tabSignature,
                'allowedTabs' => implode(', ', $allowedTabs)
            ];
        }
        sort($result);

        Console::br();
        if (count($result) > 0) {
            Console::info('Allowed tabs');
            Console::table($result, ['Package signature', 'Allowed tabs']);
        } else {
            Console::info('No allowed tabs registered.');
        }
    }
}