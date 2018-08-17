<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Composer\Repository\Config;
use CoRex\Helpers\Obj;

class Tabs
{
    const DETAILS = 'details';
    const README = 'readme';
    const CHANGELOG = 'changelog';
    const COMPOSER = 'composer';
    const FILES = 'files';

    /**
     * Available.
     *
     * @return array
     */
    public static function available()
    {
        return Obj::getConstants(self::class);
    }

    /**
     * Get tabs for signature.
     *
     * @param string $signature
     * @return array
     */
    public static function getSignature($signature)
    {
        return Config::load()->getSignatureTabs($signature);
    }

    /**
     * Get signature allowed.
     *
     * @param string $signature
     * @return array
     */
    public static function getSignatureAllowed($signature)
    {
        $tabs = self::getSignature($signature);
        if (count($tabs) == 0) {
            $tabs = self::available();
        }
        return $tabs;
    }

    /**
     * Set tabs for signature.
     *
     * @param string $signature
     * @param array $tabs
     */
    public static function setSignature($signature, array $tabs)
    {
        Config::load()->setSignatureTabs($signature, $tabs)->save();
    }

    /**
     * Has tab access for signature.
     *
     * @param string $signature
     * @param string $tab
     * @return boolean
     */
    public static function hasAccess($signature, $tab)
    {
        return Config::load()->hasSignatureTab($signature, $tab);
    }
}