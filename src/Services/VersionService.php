<?php

namespace CoRex\Composer\Repository\Services;

use CoRex\Composer\Repository\Config;
use CoRex\Filesystem\File;

class VersionService
{
    /** @var string */
    private $filename;

    /** @var array|mixed[] */
    private $versions;

    /** @var array|mixed[] */
    private $versionsNew;

    /**
     * VersionService.
     */
    public function __construct()
    {
        $this->filename = Config::load()->getPath(['versions.json']);
        $this->versions = File::getJson($this->filename);
        $this->versionsNew = [];
        $this->versionLatest = [];
    }

    /**
     * Satis package.
     *
     * @return static
     */
    public static function load(): self
    {
        return new static();
    }

    /**
     * Save.
     */
    public function save(): void
    {
        File::putJson($this->filename, $this->versions);
    }

    /**
     * Set version.
     *
     * @param string $signature
     * @param string $version
     */
    public function setVersion(string $signature, string $version): void
    {
        if (!array_key_exists($signature, $this->versions)) {
            $this->versions[$signature] = [];
        }
        if (!in_array($version, $this->versions[$signature])) {
            $this->versions[$signature][] = $version;

            // Add new version.
            if (!array_key_exists($signature, $this->versionsNew)) {
                $this->versionsNew[$signature] = [];
            }
            $this->versionsNew[$signature][] = $version;
        }
    }

    /**
     * Get new versions.
     *
     * @return mixed[]
     */
    public function getNewVersions(): array
    {
        return $this->versionsNew;
    }
}