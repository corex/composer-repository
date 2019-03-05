<?php

namespace CoRex\Composer\Repository\Services;

use CoRex\Composer\Repository\Helpers\PackageVersion;
use CoRex\Filesystem\File;
use CoRex\Helpers\Arr;

class PackageService
{
    private $signature;
    private $data;

    /**
     * PackageService constructor.
     *
     * @param string $signature
     */
    public function __construct($signature)
    {
        $this->signature = $signature;

        // Load data.
        $filename = PackagesService::load()->getPackageFilename($this->signature);
        $this->data = File::getJson($filename);
    }

    /**
     * Satis package.
     *
     * @param string $signature
     * @return static
     */
    public static function load($signature)
    {
        return new static($signature);
    }

    /**
     * Exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return count($this->data) > 0;
    }

    /**
     * Versions.
     *
     * @param boolean $reversed
     * @param bool $filterOutDev
     * @return array
     */
    public function getVersions($reversed = false, $filterOutDev = true)
    {
        $versions = array_keys($this->getVersionEntities());
        if ($filterOutDev) {
            foreach ($versions as $index => $version) {
                if (is_int(strpos($version, 'dev'))) {
                    unset($versions[$index]);
                }
            }
            $versions = array_values($versions);
        }
        if ($reversed) {
            rsort($versions);
        } else {
            sort($versions);
        }
        return $versions;
    }

    /**
     * Latest version.
     *
     * @param bool $filterOutDev
     * @return string
     */
    public function getLatestVersion($filterOutDev = true)
    {
        $versions = $this->getVersions(true, $filterOutDev);
        if (count($versions) > 0) {
            return $versions[0];
        }
        return null;
    }

    /**
     * Version.
     *
     * @param string $version
     * @return PackageVersion
     */
    public function getVersionEntity($version)
    {
        if (in_array($version, ['.', '*', '-'])) {
            $version = $this->getLatestVersion();
        }
        $versionEntities = $this->getVersionEntities();
        if (isset($versionEntities[$version])) {
            return new PackageVersion($versionEntities[$version]);
        }
        return null;
    }

    /**
     * Version entities.
     *
     * @return array
     */
    public function getVersionEntities()
    {
        return Arr::get($this->data, 'packages.' . $this->signature, []);
    }
}