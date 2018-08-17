<?php

namespace CoRex\Composer\Repository\Services;

use CoRex\Composer\Repository\Config;
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
        $filename = $this->filename();
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
     * @return array
     */
    public function getVersions($reversed = false)
    {
        $versions = array_keys($this->getVersionEntities());
        foreach ($versions as $index => $version) {
            if (is_int(strpos($version, 'dev'))) {
                unset($versions[$index]);
            }
        }
        $versions = array_values($versions);
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
     * @return string
     */
    public function getLatestVersion()
    {
        $versions = $this->getVersions(true);
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

    /**
     * Filename.
     *
     * @return string
     */
    private function filename()
    {
        $config = Config::load();
        $packageHash = PackagesService::load()->getPackageHash($this->signature);
        if ($packageHash !== null) {
            $segments = [
                'p',
                $this->signature . '$' . $packageHash . '.json'
            ];
            return $config->getPath($segments);
        }
        return null;
    }
}