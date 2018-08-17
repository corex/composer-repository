<?php

namespace CoRex\Composer\Repository\Commands;

use Composer\Satis\Console\Command\BuildCommand as SatisBuildCommand;
use CoRex\Composer\Repository\Browser;
use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
use CoRex\Composer\Repository\Path;
use CoRex\Composer\Repository\Services\PackageService;
use CoRex\Composer\Repository\Services\PackagesService;
use CoRex\Composer\Repository\Services\PackagistService;
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
        Message::header($this->getDescription());

        $config = Config::load();
        if ($config->getPath() === null) {
            Message::error('Path not set. Run command "config:path" to set path (must be accessible via web).');
        }

        // Create browser files.
        Message::info('Creating basic browser files.');
        $vendorDirectory = PackagistService::getVendorDirectory();
        $this->createBrowserFiles($vendorDirectory);

        // Load configuration.
        $path = $config->getPath();
        if ($path === null) {
            Message::error('Path not set. Run command "config:path".');
        }
        if (!Directory::exist($path)) {
            Message::error('Path ' . $path . ' does not exist.');
        }

        // Get and validate packages.
        $repositories = $config->getPackageRepositories();
        $requires = $config->getPackageRequires();
        if (count($requires) == 0) {
            Message::error('Cannot build. No packages added.');
        }

        // Write temp "satis.json".
        $buildFilename = File::getTempFilename(Directory::temp(), 'satis-', 'json');
        $json = new Json($buildFilename);
        $json->clear();
        $json->set('name', $config->getName());
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

        // Remove generated "satis.json".
        File::delete($buildFilename);

        // Scanning archives.
        Message::info('Scanning archives');

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
                        Message::info('Scanning archive for ' . $signature . ' ' . $version);
                        $packageMap->scan();
                    }

                    // Only set for latest version.
                    if ($version == $latestVersion) {
                        // TODO For the future.
                    }
                }
            }
        }

        Message::info('Building and mapping done.');

        return $result;
    }

    /**
     * Create browser files.
     *
     * @param string $vendorDirectory
     */
    private function createBrowserFiles($vendorDirectory)
    {
        $config = Config::load();

        // Create ".htaccess".
        $lines = [
            '<IfModule mod_rewrite.c>',
            'RewriteEngine On',
            'RewriteBase /',
            'RewriteRule ^index\.php$ - [L]',
            'RewriteCond %{REQUEST_FILENAME} !-f',
            'RewriteCond %{REQUEST_FILENAME} !-d',
            'RewriteRule . /index.php [L]',
            '</IfModule>'
        ];
        File::putLines($config->getPath(['.htaccess']), $lines);

        // Create "index.php".
        $lines = [
            '<' . '?php',
            'require_once(\'' . $vendorDirectory . '/autoload.php' . '\');',
            '\\' . Browser::class . '::run();'
        ];
        File::putLines($config->getPath(['index.php']), $lines);

        // Copy stylesheet from "scrivo/highlight.php".
        $stylesheet = 'github-gist.css';
        $filename = Path::packages(['scrivo', 'highlight.php', 'styles', $stylesheet]);
        File::copy($filename, Config::load()->getPath());
    }
}