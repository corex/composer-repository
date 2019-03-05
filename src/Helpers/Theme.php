<?php

namespace CoRex\Composer\Repository\Helpers;


use CoRex\Helpers\Obj;
use CoRex\Site\Helpers\Bootstrap;

class Theme
{
    /**
     * All.
     *
     * @return array
     */
    public static function all()
    {
        $names = [];
        $constants = Obj::getConstants(Bootstrap::class);
        foreach ($constants as $constant => $details) {
            if (is_array($details) && isset($details['name'])) {
                $names[] = $details['name'];
            }
        }
        return $names;
    }

    /**
     * All as string.
     *
     * @param string $separator
     * @return string
     */
    public static function allAsString(string $separator = ', '): string
    {
        return implode($separator, self::all());
    }

    /**
     * Is valid.
     *
     * @param string $theme
     * @return bool
     */
    public static function isValid($theme)
    {
        return in_array($theme, self::all());
    }

    /**
     * Theme details.
     *
     * @param string $theme
     * @return array|string[]|null
     */
    public static function details($theme)
    {
        return Bootstrap::getThemeConstant($theme);
    }
}