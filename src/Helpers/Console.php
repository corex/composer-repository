<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Composer\Repository\Helpers\Console\Writer;
use CoRex\Terminal\Console as TerminalConsole;

class Console extends TerminalConsole
{
    /**
     * Set quiet.
     *
     * @param bool $isQuiet
     */
    public static function setQuiet(bool $isQuiet): void
    {
        $climate = Console::climate();
        if ($isQuiet) {
            if (!array_key_exists('davs', $climate->output->getAvailable())) {
                $climate->output->add('hidden', new Writer());
                $climate->output->defaultTo('hidden');
            }
        } else {
            $climate->output->defaultTo('out');
        }
    }

    /**
     * Throw error.
     *
     * @param string|string[] $messages
     */
    public static function throwError($messages): void
    {
        self::error($messages);
        exit;
    }
}