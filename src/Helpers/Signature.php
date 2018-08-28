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
        $parts = [
            'vendor' => null,
            'package' => null
        ];
        if ((string)$signature != '') {
            $signatureParts = explode('/', (string)$signature);
            if (count($signatureParts) == 2) {
                $parts = [
                    'vendor' => isset($signatureParts[0]) ? $signatureParts[0] : null,
                    'package' => isset($signatureParts[1]) ? $signatureParts[1] : null
                ];
            }
        }
        return $parts;
    }
}