<?php

namespace CoRex\Composer\Repository\Helpers;

use Composer\Factory;
use Composer\IO\NullIO;

class Composer
{
    /**
     * Get composer.
     *
     * @return \Composer\Composer
     */
    public static function getComposer()
    {
        return Factory::create(new NullIO(), array());
    }

    /**
     * Get config.
     *
     * @return \Composer\Config
     */
    public static function getConfig()
    {
        return self::getComposer()->getConfig();
    }

    /**
     * Get vendor directory.
     *
     * @return string
     */
    public static function getVendorDirectory()
    {
        return self::getConfig()->get('vendor-dir');
    }
}