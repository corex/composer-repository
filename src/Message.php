<?php

namespace CoRex\Composer\Repository;

use CoRex\Support\System\Console;

class Message
{
    /**
     * Error (stops code = exit).
     *
     * @param string $message
     */
    public static function error($message)
    {
        Console::error($message);
        exit;
    }

    /**
     * Header.
     *
     * @param string $message
     */
    public static function header($message)
    {
        Console::header(Config::load()->getName() . ' - ' . $message);
    }

    /**
     * Info.
     *
     * @param string $message
     */
    public static function info($message)
    {
        Console::info($message);
    }

    /**
     * Warning.
     *
     * @param string $message
     */
    public static function warning($message)
    {
        Console::warning($message);
    }

    /**
     * Writeln.
     *
     * @param string $message
     */
    public static function writeln($message)
    {
        Console::writeln($message);
    }

    /**
     * Blank line.
     *
     * @param integer $numberOfBlankLines
     */
    public static function blank($numberOfBlankLines = 1)
    {
        for ($c1 = 0; $c1 < $numberOfBlankLines; $c1++) {
            Console::writeln('');
        }
    }
}