<?php

namespace CoRex\Composer\Repository;

use Composer\Command\BaseCommand;
use Composer\Satis\Console\Application as SatisApplication;
use Composer\Satis\Console\Command\AddCommand as SatisAddCommand;
use Composer\Satis\Console\Command\BuildCommand as SatisBuildCommand;
use Composer\Satis\Console\Command\InitCommand as SatisInitCommand;
use Composer\Satis\Console\Command\PurgeCommand as SatisPurgeCommand;
use CoRex\Composer\Repository\Commands;
use CoRex\Composer\Repository\Helpers\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends SatisApplication
{
    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName(Config::load()->getName());
        $this->setVersion('');
    }

    /**
     * Get default commands.
     *
     * @return array|Command[]
     * @throws \Exception
     */
    protected function getDefaultCommands()
    {
        $commands = [];

        $commands[] = new HelpCommand();
        $commands[] = new ListCommand();

        // Satis commands.
        $commands[] = $this->prepareSatisCommand(new SatisInitCommand());
        $commands[] = $this->prepareSatisCommand(new SatisAddCommand());
        $commands[] = $this->prepareSatisCommand(new SatisBuildCommand());
        $commands[] = $this->prepareSatisCommand(new SatisPurgeCommand());

        // Basic commands.
        $commands[] = new Commands\BuildCommand();
        $commands[] = new Commands\ClearCommand();
        $commands[] = new Commands\InitCommand();
        $commands[] = new Commands\OrderCommand();
        $commands[] = new Commands\TestCommand();

        // Config commands.
        $commands[] = new Commands\Config\HomepageCommand();
        $commands[] = new Commands\Config\NameCommand();
        $commands[] = new Commands\Config\PathCommand();
        $commands[] = new Commands\Config\ShowCommand();
        $commands[] = new Commands\Config\TabsCommand();
        $commands[] = new Commands\Config\ThemeCommand();
        $commands[] = new Commands\Config\EmailFromCommand();
        $commands[] = new Commands\Config\EmailToAddCommand();
        $commands[] = new Commands\Config\EmailToRemoveCommand();

        // Namespace commands.
        $commands[] = new Commands\Namespaces\AddCommand();
        $commands[] = new Commands\Namespaces\RemoveCommand();
        $commands[] = new Commands\Namespaces\ShowCommand();

        // Package commands.
        $commands[] = new Commands\Package\AddCommand();
        $commands[] = new Commands\Package\RemoveCommand();

        // Show commands.
        $commands[] = new Commands\Show\AllCommand();
        $commands[] = new Commands\Show\PackagesCommand();
        $commands[] = new Commands\Show\VendorsCommand();
        $commands[] = new Commands\Show\VersionsCommand();

        return $commands;
    }

    /**
     * Do run.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        Console::setQuiet($output->isQuiet());
        return parent::doRun($input, $output);
    }

    /**
     * Prepare command.
     *
     * @param BaseCommand $command
     * @return BaseCommand
     */
    private function prepareSatisCommand(BaseCommand $command)
    {
        // Add 'satis:' to name of command.
        $command->setName('satis:' . $command->getName());
        $command->setHidden(true);
        return $command;
    }
}