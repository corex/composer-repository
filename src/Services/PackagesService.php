<?php

namespace CoRex\Composer\Repository\Services;

use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Helpers\Signature;
use CoRex\Filesystem\File;

class PackagesService
{
    const HASHING_ALGORITHM = 'sha256';

    private static $instance;
    private $path;
    private $repositories;

    /**
     * PackagesService constructor.
     */
    public function __construct()
    {
        $config = Config::load();
        $config->validate();
        $this->path = $config->getPath();
        $this->loadPackages();
    }

    /**
     * Load.
     *
     * @return static
     */
    public static function load()
    {
        if (!is_object(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Vendor names.
     *
     * @return array
     */
    public function getVendorNames()
    {
        return array_keys($this->repositories);
    }

    /**
     * Package names.
     *
     * @param string $vendor
     * @param boolean $namesAsKeyValue
     * @return array
     */
    public function getPackageNames($vendor, $namesAsKeyValue = false)
    {
        $result = [];
        if (isset($this->repositories[$vendor])) {
            $packages = array_keys($this->repositories[$vendor]);
            if ($namesAsKeyValue) {
                foreach ($packages as $package) {
                    $result[] = ['vendor' => $vendor, 'package' => $package];
                }
            } else {
                $result = $packages;
            }
            sort($result);
        }
        return $result;
    }

    /**
     * Package.
     *
     * @param string $signature
     * @return PackageService
     */
    public function getPackage($signature)
    {
        return PackageService::load($signature);
    }

    /**
     * Package hash.
     *
     * @param string $signature
     * @return string
     */
    public function getPackageHash($signature)
    {
        $vendor = Signature::vendor($signature);
        $package = Signature::package($signature);
        if (isset($this->repositories[$vendor][$package])) {
            return $this->repositories[$vendor][$package][self::HASHING_ALGORITHM];
        }
        return null;
    }

    /**
     * Load packages.
     */
    private function loadPackages()
    {
        $this->repositories = [];
        $filename = $this->path . '/packages.json';
        $packages = File::getJson($filename);

        // Extract providers.
        $providers = [];
        if (array_key_exists('providers', $packages)) {
            $providers = $packages['providers'];
        }
        foreach ($providers as $signature => $packageDetails) {
            if (!Signature::isValid($signature)) {
                continue;
            }
            $vendor = Signature::vendor($signature);
            $package = Signature::package($signature);
            $this->repositories[$vendor][$package] = $packageDetails;
        }
    }
}