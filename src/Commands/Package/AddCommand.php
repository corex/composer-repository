<?php

namespace CoRex\Composer\Repository\Commands\Package;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Build;
use CoRex\Composer\Repository\Helpers\Signature;
use CoRex\Composer\Repository\Message;
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
        Message::header($this->getDescription());

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
            Message::info('Build ordered.');
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
        Message::info('Adding package ' . $signature);

        if (!PackagistService::packagistHasPackage($signature)) {
            Message::error('Package ' . $signature . ' not found.');
        }

        // Add package.
        Message::blank();
        $config = Config::load();
        $isAdded = $config->addPackageSignature($signature);
        if ($isAdded) {
            $config->save();
            Message::info('Package added.');
            return true;
        } else {
            Message::error('Package already added.');
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
        Message::info('Adding package ' . $repositoryUrl);

        $composerInformation = PackagistService::getRepositoryInformationByUrl($repositoryUrl);
        if (empty($composerInformation['name'])) {
            Message::error('Not a valid composer package.');
        }
        $signature = $composerInformation['name'];

        // Add package.
        Message::blank();
        $config = Config::load();
        $isAdded = $config->addPackageUrl($signature, $repositoryUrl);
        if ($isAdded) {
            $config->save();
            Message::info('Package added.');
            return true;
        } else {
            Message::error('Package already added.');
            return false;
        }
    }
}