<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Helpers\Arr;

class PackageVersion
{
    private $data;

    /**
     * SatisPackageVersion constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getSignature()
    {
        return Arr::get($this->data, 'name');
    }

    /**
     * Get value.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getValue($name, $default = null)
    {
        return Arr::get($this->data, $name, $default);
    }

    /**
     * Get source url.
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return Arr::get($this->data, 'source.url');
    }

    /**
     * Get reference.
     *
     * @return string
     */
    public function getReference()
    {
        return Arr::get($this->data, 'dist.reference');
    }

    /**
     * Get require.
     *
     * @return array
     */
    public function getRequires()
    {
        $require = Arr::get($this->data, 'require', []);
        foreach ($require as $key => $value) {
            if (is_int(strpos($key, '/'))) {
                continue;
            }
            unset($require[$key]);
        }
        return $require;
    }

    /**
     * Get keywords.
     *
     * @return array
     */
    public function getKeywords()
    {
        return Arr::get($this->data, 'keywords', []);
    }

    /**
     * Archive.
     *
     * @return Archive
     */
    public function getArchive()
    {
        $distUrl = Arr::get($this->data, 'dist.url');
        if ($distUrl === null) {
            return null;
        }

        return new Archive($this->getSignature(), $this->getReference(), $distUrl);
    }

    /**
     * Get map.
     *
     * @return Map
     */
    public function getMap()
    {
        $archive = $this->getArchive();
        if ($archive !== null) {
            return new Map($archive, $this);
        }
        return null;
    }

    /**
     * Get "composer.json".
     *
     * @return string
     */
    public function getComposerJson()
    {
        return json_encode($this->data, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);
    }
}