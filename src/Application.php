<?php

namespace CoRex\Composer\Repository;

use Composer\Command\BaseCommand;
use Composer\Satis\Console\Application as SatisApplication;
use Composer\Satis\Console\Command\AddCommand as SatisAddCommand;
use Composer\Satis\Console\Command\BuildCommand as SatisBuildCommand;
use Composer\Satis\Console\Command\InitCommand as SatisInitCommand;
use Composer\Satis\Console\Command\PurgeCommand as SatisPurgeCommand;
use CoRex\Composer\Repository\Commands;
use CoRex\Composer\Repository\Commands\TestCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;

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

        // Config commands.
        $commands[] = new Commands\Config\HomepageCommand();
        $commands[] = new Commands\Config\NameCommand();
        $commands[] = new Commands\Config\PathCommand();
        $commands[] = new Commands\Config\ShowCommand();

        // Package commands.
        $commands[] = new Commands\Package\AddNameCommand();
        $commands[] = new Commands\Package\AddUrlCommand();
        $commands[] = new Commands\Package\RemoveCommand();

        // Show commands.
        $commands[] = new Commands\Show\AllCommand();
        $commands[] = new Commands\Show\ChangelogCommand();
        $commands[] = new Commands\Show\LicenseCommand();
        $commands[] = new Commands\Show\ReadmeCommand();
        $commands[] = new Commands\Show\PackagesCommand();
        $commands[] = new Commands\Show\VendorsCommand();
        $commands[] = new Commands\Show\VersionsCommand();

        return $commands;
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