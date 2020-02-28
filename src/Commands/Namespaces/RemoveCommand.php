<?php

namespace CoRex\Composer\Repository\Commands\Namespaces;

use Composer\Command\BaseCommand;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Services\NamespaceService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends BaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('namespace:remove');
        $this->setDescription('Remove namespace');
        $this->addArgument('package-prefix', InputArgument::REQUIRED, 'Prefix for package i.e. "corex/service-".');
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

        $prefix = $input->getArgument('package-prefix');
        $namespaceService = NamespaceService::load();

        if ($namespaceService->has($prefix)) {
            // Remove namespace.
            $namespace = $namespaceService->get($prefix);
            $namespaceService->remove($prefix);
            $namespaceService->save();

            Console::info('Namespace "' . $namespace . '" removed on prefix "' . $prefix . '".');
        } else {
            Console::error('Prefix "' . $prefix . '" not added.');
        }
    }
}