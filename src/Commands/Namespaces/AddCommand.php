<?php

declare(strict_types=1);

namespace CoRex\Composer\Repository\Commands\Namespaces;

use Composer\Command\BaseCommand;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Helpers\Signature;
use CoRex\Composer\Repository\Services\NamespaceService;
use CoRex\Helpers\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends BaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('namespace:add');
        $this->setDescription('Add namespace');
        $this->addArgument('package-prefix', InputArgument::REQUIRED, 'Prefix for package i.e. "corex/service-".');
        $this->addArgument('namespace', InputArgument::REQUIRED, 'Namespace for project ("/" for separator)');
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Console::header($this->getDescription());

        $prefix = $input->getArgument('package-prefix');
        $namespace = $input->getArgument('namespace');

        // Prepare package prefix.
        if (!Signature::isValid($prefix)) {
            Console::throwError('Prefix "' . $prefix . '" is not valid.');
        }

        // Prepare namespace.
        $namespace = str_replace(['\\', ';', ':', '-', '.', ','], '|', $namespace);
        if (Str::contains($namespace, '|')) {
            Console::throwError('Namespace contains illegal characters. Use "/" for separator.');
        }
        $namespace = str_replace('/', '\\', $namespace);
        $namespaceParts = array_filter(explode('|', $namespace));
        $namespaceParts = array_map(function ($value) {
            return ucfirst($value);
        }, $namespaceParts);
        $namespace = implode('\\', $namespaceParts);

        $namespaceService = NamespaceService::load();
        if (!$namespaceService->has($prefix)) {
            $question = 'Are you sure you want to add namespace "' . $namespace . " on prefix " . $prefix . '"';
            if (Console::confirm($question)) {
                $namespaceService->add($prefix, $namespace);
                $namespaceService->save();
                Console::info('Namespace "' . $namespace . '" added on prefix "' . $prefix . '".');
            }
        } else {
            Console::error('Namespace "' . $namespace . '" already added.');
        }

        return 0;
    }
}