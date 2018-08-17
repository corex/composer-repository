<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Constants;
use CoRex\Composer\Repository\Message;
use CoRex\Support\System\Console;
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
        Message::header($this->getDescription());

        $config = Config::load();
        Console::properties([
            'Name' => $config->getName(),
            'Homepage' => $config->getHomepage(),
            'Path' => $config->getPath()
        ]);

        // Render result.
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

        Message::blank();
        if (count($result) > 0) {
            Message::info('Packages');
            Console::table($result, ['Package signature', 'Repository url']);
        } else {
            Message::info('No packages registered.');
        }
    }
}