<?php

namespace CoRex\Composer\Repository\Commands;

use Composer\Satis\Console\Command\BuildCommand as SatisBuildCommand;
use CoRex\Composer\Repository\Browser;
use CoRex\Composer\Repository\Browser\Url;
use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Build;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Helpers\Mailer;
use CoRex\Composer\Repository\Services\PackageService;
use CoRex\Composer\Repository\Services\PackagesService;
use CoRex\Composer\Repository\Services\VersionService;
use CoRex\Filesystem\Directory;
use CoRex\Filesystem\File;
use CoRex\Filesystem\Json;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends SatisBuildCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('build');
        $this->setDescription('Build data');
        $this->setHelp('');
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Console::header($this->getDescription());

        $config = Config::load();
        if ($config->getPath() === null) {
            Console::throwError('Path not set. Run command "config:path" to set path (must be accessible via web).');
        }

        // Create browser files.
        Console::info('Creating basic browser files.');
        Browser::createBrowserFiles();

        // Load configuration.
        $path = $config->getPath();
        if ($path === null) {
            Console::throwError('Path not set. Run command "config:path".');
        }
        if (!Directory::exist($path)) {
            Console::throwError('Path ' . $path . ' does not exist.');
        }

        // Get and validate packages.
        $repositories = $config->getPackageRepositories();
        $requires = $config->getPackageRequires();
        if (count($requires) == 0) {
            Console::throwError('Cannot build. No packages added.');
        }

        // Check if build already running.
        if (Build::isRunning()) {
            Console::info('Build already running. Skipping.');
            return 0;
        }

        // Check if orders are available.
        if (!Build::isOrdered()) {
            Console::info('Build not ordered.');
            return 0;
        }

        Build::markRunning();

        // Write temp "satis.json".
        $buildFilename = File::getTempFilename(Directory::temp(), 'satis-', 'json');

        // Remove generated "satis.json".
        if (File::exist($buildFilename)) {
            File::delete($buildFilename);
        }

        // Generate new "satis.json".
        $json = new Json($buildFilename);
        $json->clear();
        $json->set('name', $config->getPackageName());
        $json->set('homepage', $config->getHomepage());
        $json->set('repositories', $repositories);
        $json->setBool('require-all', false);
        $json->setBool('output-html', false);
        $json->setBool('providers', true);
        $json->setBool('require-dependencies', true);
        $json->setBool('require-dev-dependencies', false);
        $json->set('output-dir', $config->getPath());
        $json->set('archive', [
            'directory' => 'dist',
            'format' => 'zip',
            'skip-dev' => true
        ]);
        $json->set('require', $requires);
        $json->save();

        // Call Satis Build command to generate basic Satis data.
        $input->setArgument('file', $buildFilename);
        $result = parent::execute($input, $output);

        // Scanning archives.
        Console::info('Scanning archives');

        $versionService = VersionService::load();
        $packagesService = PackagesService::load();
        $vendorNames = $packagesService->getVendorNames();
        foreach ($vendorNames as $vendorName) {
            $packageNames = $packagesService->getPackageNames($vendorName);
            foreach ($packageNames as $packageName) {
                $signature = $vendorName . '/' . $packageName;

                $packageService = PackageService::load($signature);
                $latestVersion = $packageService->getLatestVersion();
                $versions = $packageService->getVersions();

                foreach ($versions as $version) {
                    $packageVersion = $packageService->getVersionEntity($version);

                    // Scanning archive.
                    $packageMap = $packageVersion->getMap();
                    if (!$packageMap->exists()) {
                        Console::info('Scanning archive for ' . $signature . ' ' . $version);
                        $packageMap->scan();
                    }

                    $versionService->setVersion($signature, $version);

                    // Only set for latest version.
                    if ($version == $latestVersion) {
                        // TODO For the future.
                    }
                }
            }
        }
        $versionService->save();

        // Send new or updated packages.
        $registeredPackages = $config->getPackageSignatures();
        $newPackages = $versionService->getNewPackages();
        $newVersions = $versionService->getNewVersions();
        $updatedPackages = array_keys($newVersions);
        if (count($newVersions) > 0 && !$versionService->isFirstRun()) {
            $mailer = new Mailer();
            $mailer->setHtml();
            $mailer->subject($config->getName() . ' - new/updated packages');
            $mailer->text('Click on signature to go to package in browser.');
            $mailer->br();

            // Notify about new packages.
            $mailer->text('<strong>New packages.</strong>');
            foreach ($newPackages as $signature) {
                if (!in_array($signature, $registeredPackages)) {
                    continue;
                }
                $url = Url::build(['controller' => 'package', 'signature' => $signature]);
                $line = '<a href="' . $url . '">' . $signature . '</a>';
                $mailer->text($line);
            }

            $mailer->br();

            // Notify about updated packages.
            $mailer->text('<strong>Updated packages.</strong>');
            foreach ($updatedPackages as $signature) {
                if (!in_array($signature, $registeredPackages)) {
                    continue;
                }
                $url = Url::build(['controller' => 'package', 'signature' => $signature]);
                $line = '<a href="' . $url . '">' . $signature . '</a>';
                $mailer->text($line);
            }

            $mailer->send();
        }

        Console::info('Building and mapping done based on ' . $buildFilename);

        Build::markRunningDone();

        return $result;
    }
}