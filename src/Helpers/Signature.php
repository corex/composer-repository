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
        $signature = (string)$signature;
        $parts = [
            'vendor' => null,
            'package' => null
        ];
        if ($signature != '') {
            $illegalParts = ['@', ':', '.git'];
            $signatureParts = explode('/', $signature);
            if (count($signatureParts) == 2 && !self::containsParts($signature, $illegalParts)) {
                $parts = [
                    'vendor' => isset($signatureParts[0]) ? $signatureParts[0] : null,
                    'package' => isset($signatureParts[1]) ? $signatureParts[1] : null
                ];
            }
        }
        return $parts;
    }

    /**
     * Contains parts.
     *
     * @param string $signature
     * @param array $parts
     * @return bool
     */
    private static function containsParts($signature, array $parts)
    {
        foreach ($parts as $part) {
            if (is_int(strpos($signature, $part))) {
                return true;
            }
        }
        return false;
    }
}