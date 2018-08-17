<?php

namespace CoRex\Composer\Repository\Commands\Package;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Message;
use CoRex\Composer\Repository\Services\PackagistService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUrlCommand extends Command
{
    private $definition;

    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->definition = $this->getDefinition();
        $this->setName('package:add-url');
        $this->setDescription('Add package by repository-url');
        $this->setDefinition([
            new InputArgument('repository-url', InputArgument::REQUIRED, 'Url of package repository')
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

        $repositoryUrl = $input->getArgument('repository-url');

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
        } else {
            Message::error('Package already added.');
        }
    }
}