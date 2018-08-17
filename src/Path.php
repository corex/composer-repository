<?php

namespace CoRex\Composer\Repository;

use CoRex\Support\Arr;
use CoRex\Support\System\Path as SupportPath;

class Path extends SupportPath
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
     * Package path.
     *
     * @return string
     */
    protected static function packagePath()
    {
        return dirname(__DIR__);
    }
}