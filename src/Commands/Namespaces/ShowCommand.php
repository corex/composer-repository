<?php

declare(strict_types=1);

namespace CoRex\Composer\Repository\Commands\Namespaces;

use Composer\Command\BaseCommand;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Services\NamespaceService;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends BaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('namespace:show');
        $this->setDescription('Show namespaces');
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Console::header($this->getDescription());

        $namespaceServices = NamespaceService::load();
        $rows = $namespaceServices->getAll();

        // Show list of namespaces.
        $table = new Table($output);
        $table->setHeaders(['Prefix', 'Namespace']);
        $table->addRows($rows);
        $table->render();

        return 0;
    }
}