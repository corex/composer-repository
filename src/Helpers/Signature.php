<?php

namespace CoRex\Composer\Repository\Helpers;

class Signature
{
    /**
     * Vendor.
     *
     * @param string $signature
     * @return string
     */
    public static function vendor($signature)
    {
        $data = self::split($signature);
        return $data['vendor'];
    }

    /**
     * Package.
     *
     * @param string $signature
     * @return string
     */
    public static function package($signature)
    {
        $data = self::split($signature);
        return $data['package'];
    }

    /**
     * Is valid.
     *
     * @param string $signature
     * @return boolean
     */
    public static function isValid($signature)
    {
        $data = self::split($signature);
        return $data['vendor'] !== null && $data['package'] !== null;
    }

    /**
     * Split.
     *
     * @param string $signature
     * @return array
     */
    public static function split($signature)
    {
        $parts = [];
        if ((string)$signature != '') {
            $signature = trim($signature, '/');
            $parts = explode('/', (string)$signature);
        }
        return [
            'vendor' => isset($parts[0]) ? $parts[0] : null,
            'package' => isset($parts[1]) ? $parts[1] : null
        ];
    }
}