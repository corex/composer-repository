<?php

namespace CoRex\Composer\Repository\Commands\Show;

use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Services\PackageService;
use CoRex\Composer\Repository\Services\PackagesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AllCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('show:all');
        $this->setDescription('Show all packages');
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Console::header($this->getDescription());

        $result = [];
        $packages = PackagesService::load();
        $vendorNames = $packages->getVendorNames();
        if (count($vendorNames) > 0) {
            foreach ($vendorNames as $vendorName) {
                $packageNames = $packages->getPackageNames($vendorName);
                foreach ($packageNames as $packageName) {
                    $package = PackageService::load($vendorName . '/' . $packageName);
                    $latestVersion = $package->getLatestVersion();
                    if ($latestVersion !== null) {
                        $versionEntity = $package->getVersionEntity($latestVersion);
                        $sourceUrl = $versionEntity->getSourceUrl();
                    } else {
                        // Not able to find a version, searching for dev-version.
                        $latestVersion = $package->getLatestVersion(false);
                        $versionEntity = $package->getVersionEntity($latestVersion);
                        $sourceUrl = $versionEntity->getSourceUrl();
                    }
                    $result[] = [
                        'signature' => $vendorName . '/' . $packageName,
                        'latestVersion' => $latestVersion,
                        'sourceUrl' => $sourceUrl
                    ];
                }
            }
            Console::table($result, ['Signature', 'Latest', 'Source url']);
        } else {
            Console::info('No packages registered.');
        }
    }
}