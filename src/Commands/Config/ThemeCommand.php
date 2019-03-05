<?php

namespace CoRex\Composer\Repository\Commands\Config;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Helpers\Theme;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeCommand extends Command
{
    /**
     * Configure-
     */
    protected function configure()
    {
        $this->setName('config:theme');
        $this->setDescription('Set repository theme');
        $this->setDefinition([
            new InputArgument('theme', InputArgument::REQUIRED,
                'Name of theme. Available themes: ' . Theme::allAsString()),
        ]);
    }

    /**
     * Execute.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $theme = $input->getArgument('theme');

        if (!Theme::isValid($theme)) {
            Console::error('Theme ' . $theme . ' is not valid.');
            Console::br();
            Console::info('Avaiable themes: ' . Theme::allAsString());
        }

        Config::load()->setTheme($theme)->save();
        Console::info('Theme ' . $theme . ' set.');
    }
}