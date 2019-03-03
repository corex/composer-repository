<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Composer\Repository\Config;
use CoRex\Filesystem\File;
use RecursiveIteratorIterator;

class Archive
{
    /**
     * @var \PharData
     */
    private $archive;

    private $signature;
    private $reference;
    private $distUrl;
    private $filename;

    /**
     * Archive constructor.
     * @param string $signature
     * @param string $reference
     * @param string $distUrl
     */
    public function __construct($signature, $reference, $distUrl)
    {
        $this->signature = $signature;
        $this->reference = $reference;
        $this->distUrl = $distUrl;

        // Prepare filename.
        $config = Config::load();
        $distFilename = ltrim(str_replace($config->getHomepage(), '', $distUrl), '/');
        $this->filename = $config->getPath([$distFilename]);

        if (File::exist($this->filename)) {
            $this->archive = new \PharData($this->filename);
        }
    }

    /**
     * Get signature.
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Get reference.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get "composer.json".
     *
     * @return string
     */
    public function getComposerJson()
    {
        return $this->getArchiveContent('composer.json');
    }

    /**
     * Get readme content.
     *
     * @return string
     */
    public function getReadmeContent()
    {
        $content = null;
        if ($this->archive !== null) {
            $filenames = $this->getArchiveFilenames();
            foreach ($filenames as $filename) {
                if (is_int(strpos(strtolower($filename), 'readme'))) {
                    return $this->getArchiveContent($filename);
                }
            }
        }
        return $content;
    }

    /**
     * Get changelog content.
     *
     * @return string
     */
    public function getChangelogContent()
    {
        $content = null;
        if ($this->archive !== null) {
            $filenames = $this->getArchiveFilenames();
            foreach ($filenames as $filename) {
                if (is_int(strpos(strtolower($filename), 'changelog'))) {
                    return $this->getArchiveContent($filename);
                }
            }
        }
        return $content;
    }

    /**
     * Get license content.
     *
     * @return string
     */
    public function getLicenseContent()
    {
        $content = null;
        if ($this->archive !== null) {
            $filenames = $this->getArchiveFilenames();
            foreach ($filenames as $filename) {
                if (is_int(strpos(strtolower($filename), 'license'))) {
                    return $this->getArchiveContent($filename);
                }
            }
        }
        return $content;
    }

    /**
     * Get filenames.
     *
     * @param string $extension
     * @return array
     */
    public function getArchiveFilenames($extension = null)
    {
        $result = [];
        if ($this->archive !== null) {
            $this->archive->rewind();
            $recursiveIteratorIterator = new RecursiveIteratorIterator($this->archive);
            foreach ($recursiveIteratorIterator as $file) {
                $addToList = true;
                if ($extension !== null && $file->getExtension() != $extension) {
                    $addToList = false;
                }
                if (!$addToList) {
                    continue;
                }
                $result[] = $this->getRelativeFilename($file);
            }
        }
        return $result;
    }

    /**
     * Get file content.
     *
     * @param string $relativeFilename
     * @return string
     */
    public function getArchiveContent($relativeFilename)
    {
        if ($this->archive !== null) {
            $this->archive->rewind();
            $recursiveIteratorIterator = new RecursiveIteratorIterator($this->archive);
            foreach ($recursiveIteratorIterator as $file) {
                if ($this->getRelativeFilename($file) == $relativeFilename) {
                    return $file->getContent();
                }
            }
        }
        return null;
    }

    /**
     * Relative filename.
     *
     * @param \SplFileInfo $fileInfo
     * @return string
     */
    private function getRelativeFilename(\SplFileInfo $fileInfo)
    {
        $filename = $fileInfo->getPath() . '/' . $fileInfo->getFilename();
        $filename = str_replace('phar://', '', $filename);
        $filename = str_replace($this->filename, '', $filename);
        return ltrim($filename, '/');
    }
}