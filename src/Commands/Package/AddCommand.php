<?php

namespace CoRex\Composer\Repository\Commands\Package;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Build;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Helpers\Signature;
use CoRex\Composer\Repository\Services\PackagistService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends Command
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('package:add');
        $this->setDescription('Add package');
        $this->setDefinition([
            new InputArgument(
                'signature-or-repository-url',
                InputArgument::REQUIRED,
                'Signature or repository-url of package'
            )
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Console::header($this->getDescription());

        $signatureOrUrl = $input->getArgument('signature-or-repository-url');

        // Determine if signature.
        if (Signature::isValid($signatureOrUrl)) {
            // Add package by signature.
            $isAdded = $this->addPackageBySignature($signatureOrUrl);
        } else {
            // Assume it is url and add package by url.
            $isAdded = $this->addPackageByUrl($signatureOrUrl);
        }

        // Order build.
        if ($isAdded) {
            Build::order();
            Console::info('Build ordered.');
        }
    }

    /**
     * Add package by signature.
     *
     * @param string $signature
     * @return boolean
     */
    private function addPackageBySignature($signature)
    {
        Console::info('Adding package ' . $signature);

        if (!PackagistService::packagistHasPackage($signature)) {
            Console::throwError('Package ' . $signature . ' not found.');
        }

        // Add package.
        Console::br();
        $config = Config::load();
        $isAdded = $config->addPackageSignature($signature);
        if ($isAdded) {
            $config->save();
            Console::info('Package added.');
            return true;
        } else {
            Console::error('Package already added.');
            return false;
        }
    }

    /**
     * Add package by repository-url.
     * @param string $repositoryUrl
     * @return boolean
     */
    private function addPackageByUrl($repositoryUrl)
    {
        Console::info('Adding package ' . $repositoryUrl);

        $composerInformation = PackagistService::getRepositoryInformationByUrl($repositoryUrl);
        if (empty($composerInformation['name'])) {
            Console::error('Not a valid composer package.');
        }
        $signature = $composerInformation['name'];

        // Add package.
        Console::br();
        $config = Config::load();
        $isAdded = $config->addPackageUrl($signature, $repositoryUrl);
        if ($isAdded) {
            $config->save();
            Console::info('Package added.');
            return true;
        } else {
            Console::error('Package already added.');
            return false;
        }
    }
}