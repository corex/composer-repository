<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Composer\Repository\Config;
use CoRex\Filesystem\File;

class Build
{
    const FILENAME_ORDER = 'build-order';
    const FILENAME_RUNNING = 'build-running';

    /**
     * Place a build order.
     */
    public static function order()
    {
        File::put(self::filename(self::FILENAME_ORDER), time());
    }

    /**
     * Mark order done.
     */
    public static function markOrderDone()
    {
        File::delete(self::filename(self::FILENAME_ORDER));
    }

    /**
     * Is ordered.
     *
     * @return bool
     */
    public static function isOrdered()
    {
        return File::exist(self::filename(self::FILENAME_ORDER))
            && !File::exist(self::filename(self::FILENAME_RUNNING));
    }

    /**
     * Get order time.
     *
     * @return string
     */
    public static function getOrderTime()
    {
        return File::get(self::filename(self::FILENAME_ORDER));
    }

    /**
     * Mark running.
     */
    public static function markRunning()
    {
        File::put(self::filename('build-running'), time());
    }

    /**
     * Mark running done.
     */
    public static function markRunningDone()
    {
        File::delete(self::filename('build-running'));
        self::markOrderDone();
    }

    /**
     * Check if running.
     *
     * @return boolean
     */
    public static function isRunning()
    {
        return File::exist(self::filename(self::FILENAME_RUNNING));
    }

    /**
     * Get building time.
     *
     * @return string|null
     */
    public static function getRunningTime()
    {
        return File::get(self::filename(self::FILENAME_RUNNING));
    }

    /**
     * Build filename.
     *
     * @param string $additional
     * @return string
     */
    private static function filename($additional)
    {
        return Config::load()->getPath([$additional]);
    }
}