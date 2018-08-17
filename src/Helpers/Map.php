<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Composer\Repository\Config;
use CoRex\Filesystem\Directory;
use CoRex\Filesystem\File;

class Map
{
    /**
     * @var Archive
     */
    private $archive;

    /**
     * @var PackageVersion
     */
    private $packageVersion;

    /**
     * @var ComposerJson
     */
    private $composerJson;

    private $signature;
    private $reference;
    private $filename;
    private $data;

    /**
     * Map constructor.
     *
     * @param Archive $archive
     * @param PackageVersion $packageVersion
     */
    public function __construct(Archive $archive, PackageVersion $packageVersion)
    {
        $this->archive = $archive;
        $this->packageVersion = $packageVersion;

        $this->signature = $this->archive->getSignature();
        $this->reference = $this->archive->getReference();

        $config = Config::load();
        $segments = ['maps', $this->signature, $this->reference . '.json'];
        $this->filename = $config->getPath($segments);

        // Make sure directory exists.
        $path = dirname($this->filename);
        if (!Directory::exist($path)) {
            Directory::make($path);
        }

        // Load map if exists.
        $this->data = File::getJson($this->filename);
    }

    /**
     * Get readme content.
     *
     * @return string
     */
    public function getReadmeContent()
    {
        return $this->archive->getReadmeContent();
    }

    /**
     * Get changelog content.
     *
     * @return string
     */
    public function getChangelogContent()
    {
        return $this->archive->getChangelogContent();
    }

    /**
     * Get license content.
     *
     * @return string
     */
    public function getLicenseContent()
    {
        return $this->archive->getLicenseContent();
    }

    /**
     * Exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return File::exist($this->filename);
    }

    /**
     * Rescan package.
     */
    public function rescan()
    {
        File::delete($this->filename);
        $this->scan();
    }

    /**
     * Scan package.
     */
    public function scan()
    {
        if (File::exist($this->filename)) {
            // Archive already scanned.
            return;
        }

        // Fetch "composer.json" for lookup.
        $composerJson = $this->archive->getComposerJson();
        if ($composerJson === null) {
            return;
        }
        $this->composerJson = new ComposerJson($composerJson);

        // Scan package and save.
        $this->scanRequires();
        File::putJson($this->filename, $this->data);
    }

    /**
     * Scan requires.
     */
    private function scanRequires()
    {
        $require = $this->composerJson->getRequire();
        foreach ($require as $key => $value) {
            if (is_int(strpos($key, '/'))) {
                continue;
            }
            unset($require[$key]);
        }
        $this->data['require'] = $require;
    }
}