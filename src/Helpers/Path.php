<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Filesystem\Path as FilesystemPath;
use CoRex\Helpers\Arr;

class Path extends FilesystemPath
{
    /**
     * Packages (vendor).
     *
     * @param array|string $segments
     * @return string
     */
    public static function packages($segments = null)
    {
        $segments = Arr::toArray($segments);
        array_unshift($segments, 'vendor');
        return static::root($segments);
    }

    /**
     * Get package path.
     * Note: if this class is extended, this method has to be overridden to give the base path.
     *
     * @return string
     */
    protected static function packagePath(): string
    {
        return dirname(dirname(__DIR__));
    }
}