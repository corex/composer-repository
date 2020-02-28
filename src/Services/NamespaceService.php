<?php

declare(strict_types=1);

namespace CoRex\Composer\Repository\Services;

use CoRex\Composer\Repository\Helpers\Path;
use CoRex\Composer\Repository\Helpers\Signature;
use CoRex\Filesystem\File;

class NamespaceService
{
    /** @var string */
    private $filename;

    private $namespaces = [];

    /**
     * VersionService.
     */
    public function __construct()
    {
        $this->filename = Path::root(['config', 'namespaces.json']);
        $this->namespaces = File::getJson($this->filename);
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
        File::putJson($this->filename, $this->namespaces);
    }

    /**
     * Has namespaces.
     *
     * @return bool
     */
    public function hasNamespaces(): bool
    {
        return count($this->namespaces) > 0;
    }

    /**
     * All.
     *
     * @return array
     */
    public function getAll(): array
    {
        // Get and sort vendors.
        $vendors = array_keys($this->namespaces);
        sort($vendors);

        $rows = [];
        if (count($vendors) > 0) {
            foreach ($vendors as $vendor) {
                $projects = array_keys($this->namespaces[$vendor]);
                sort($projects);
                foreach ($projects as $project) {
                    $rows[] = [
                        'prefix' => Signature::combine($vendor, $project),
                        'namespace' => $this->namespaces[$vendor][$project]
                    ];
                }
            }
        }

        return $rows;
    }

    /**
     * Get.
     *
     * @param string $package
     * @return string|null
     */
    public function get(string $package): ?string
    {
        if (!$this->has($package)) {
            return null;
        }

        $vendor = Signature::vendor($package);
        $project = Signature::package($package);
        return $this->namespaces[$vendor][$project];
    }

    /**
     * Has.
     *
     * @param string $package
     * @return bool
     */
    public function has(string $package): bool
    {
        $vendor = Signature::vendor($package);
        $project = Signature::package($package);
        return isset($this->namespaces[$vendor][$project]);
    }

    /**
     * Add.
     *
     * @param string $package
     * @param string $namespace
     * @return bool
     */
    public function add(string $package, string $namespace): bool
    {
        if ($this->has($package)) {
            return false;
        }

        $vendor = Signature::vendor($package);
        $project = Signature::package($package);
        $this->namespaces[$vendor][$project] = $namespace;

        return true;
    }

    /**
     * Remove.
     *
     * @param string $package
     * @return bool
     */
    public function remove(string $package): bool
    {
        if (!$this->has($package)) {
            return false;
        }

        $vendor = Signature::vendor($package);
        $project = Signature::package($package);
        unset($this->namespaces[$vendor][$project]);

        // Remove vendor if not projects exists.
        if (count($this->namespaces[$vendor]) === 0) {
            unset($this->namespaces[$vendor]);
        }

        return true;
    }

    /**
     * Is valid.
     *
     * @param string $packageToCheck
     * @param string $namespaceToCheck
     * @return bool
     */
    public function isValid(string $packageToCheck, string $namespaceToCheck): bool
    {
        // If no namespaces is specified, simply return true.
        if (count($this->namespaces) === 0) {
            return true;
        }

        // Validate package and namespace to check.
        if (empty($packageToCheck) || empty($namespaceToCheck)) {
            return false;
        }

        // Prepare namespaces lookup.
        $namespaces = [];
        $rows = $this->getAll();
        foreach ($rows as $row) {
            $prefix = $row['prefix'];
            $namespace = $row['namespace'];
            $namespaces[$prefix] = $namespace;
        }

        // Sort by longest valud.
        uksort($namespaces, function ($item1, $item2) {
            return strlen($item2) - strlen($item1);
        });

        // Match namespace and prefix.
        foreach ($namespaces as $prefix => $namespace) {
            if (substr($namespaceToCheck, 0, strlen($namespace)) === $namespace) {
                if (substr($packageToCheck, 0, strlen($prefix)) === $prefix) {
                    return true;
                }
            }
        }

        return false;
    }
}