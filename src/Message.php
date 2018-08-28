<?php

namespace CoRex\Composer\Repository;

use CoRex\Support\System\Console;

class Message
{
    private static $isQuiet = false;

    /**
     * Set quiet.
     *
     * @param boolean $isQuiet
     */
    public static function setQuiet($isQuiet)
    {
        self::$isQuiet = $isQuiet;
    }

    /**
     * Error (stops code = exit).
     *
     * @param string $message
     */
    public static function error($message)
    {
        if (!self::$isQuiet) {
            Console::error($message);
        }
        exit;
    }

    /**
     * Header.
     *
     * @param string $message
     */
    public static function header($message)
    {
        if (!self::$isQuiet) {
            Console::header(Config::load()->getName() . ' - ' . $message);
        }
    }

    /**
     * Info.
     *
     * @param string $message
     */
    public static function info($message)
    {
        if (!self::$isQuiet) {
            Console::info($message);
        }
    }

    /**
     * Warning.
     *
     * @param string $message
     */
    public static function warning($message)
    {
        if (!self::$isQuiet) {
            Console::warning($message);
        }
    }

    /**
     * Writeln.
     *
     * @param string $message
     */
    public static function writeln($message)
    {
        if (!self::$isQuiet) {
            Console::writeln($message);
        }
    }

    /**
     * Blank line.
     *
     * @param integer $numberOfBlankLines
     */
    public static function blank($numberOfBlankLines = 1)
    {
        if (!self::$isQuiet) {
            for ($c1 = 0; $c1 < $numberOfBlankLines; $c1++) {
                Console::writeln('');
            }
        }
    }
}