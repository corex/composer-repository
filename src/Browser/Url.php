<?php

namespace CoRex\Composer\Repository\Browser;

use CoRex\Composer\Repository\Config;

class Url
{
    const PACKAGIST_URL = 'https://packagist.org/';
    const PACKAGIST_PACKAGE_URL = 'https://packagist.org/packages/{signature}';

    /**
     * Home.
     *
     * @return string
     */
    public static function home()
    {
        return self::build();
    }

    /**
     * Packagist url.
     *
     * @param string $signature
     * @return string
     */
    public static function packagist($signature = null)
    {
        $url = self::PACKAGIST_URL;
        if ($signature !== null) {
            $url = str_replace('{signature}', $signature, self::PACKAGIST_PACKAGE_URL);
        }
        return $url;
    }

    /**
     * Link.
     *
     * @param string $title
     * @param array $params
     * @return string
     */
    public static function link($title, array $params = [])
    {
        $url = self::build($params);
        return '<a href="' . $url . '">' . $title . '</a>';
    }

    /**
     * Build.
     *
     * @param array $params
     * @return string
     */
    public static function build(array $params = [])
    {
        $url = Config::load()->getHomepage();
        if (count($params) > 0) {
            $queryStringParts = [];
            foreach ($params as $name => $value) {
                $queryStringParts[] = $name . '=' . urlencode($value);
            }
            $url .= !is_int(strpos($url, '?')) ? '?' : '&';
            $url .= implode('&', $queryStringParts);
        }
        return $url;
    }
}