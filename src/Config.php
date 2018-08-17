<?php

namespace CoRex\Composer\Repository;

use CoRex\Filesystem\Json;

class Config extends Json
{
    private static $instance;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $filename = Path::root(['config', 'repository.json']);
        parent::__construct($filename);
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('name', Constants::TITLE);
    }

    /**
     * Set name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->set('name', $name);
        return $this;
    }

    /**
     * Get homepage.
     *
     * @return string
     */
    public function getHomepage()
    {
        return $this->get('homepage');
    }

    /**
     * Set homepage.
     *
     * @param string $homepage
     * @return $this
     */
    public function setHomepage($homepage)
    {
        $this->set('homepage', $homepage);
        return $this;
    }

    /**
     * Get pathy.
     *
     * @param array $segments
     * @return string
     */
    public function getPath(array $segments = [])
    {
        $path = $this->get('path');
        if (count($segments) > 0) {
            $path .= '/' . implode('/', $segments);
        }
        return $path;
    }

    /**
     * Set path.
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->set('path', $path);
        return $this;
    }

    /**
     * Get packages.
     *
     * @return array
     */
    public function getPackages()
    {
        return $this->get('packages', []);
    }

    /**
     * Get package signatures.
     *
     * @return array
     */
    public function getPackageSignatures()
    {
        return array_keys($this->getPackages());
    }

    /**
     * Has package.
     *
     * @param string $signature
     * @return boolean
     */
    public function hasPackage($signature)
    {
        return in_array($signature, $this->getPackageSignatures());
    }

    /**
     * Add package signature.
     *
     * @param string $signature
     * @return boolean
     */
    public function addPackageSignature($signature)
    {
        return $this->addPackageUrl($signature, null);
    }

    /**
     * Add package url.
     *
     * @param string $signature
     * @param mixed $details
     * @return boolean
     */
    public function addPackageUrl($signature, $details)
    {
        if (!$this->hasPackage($signature)) {
            $packages = $this->get('packages', []);
            $packages[$signature] = $details;
            $this->set('packages', $packages);
            return true;
        }
        return false;
    }

    /**
     * Remove package.
     *
     * @param string $signature
     * @return boolean
     */
    public function removePackage($signature)
    {
        if ($this->hasPackage($signature)) {
            $packages = $this->get('packages', []);
            unset($packages[$signature]);
            $this->set('packages', $packages);
            return true;
        }
        return false;
    }

    /**
     * Get package repositories.
     *
     * @return array
     */
    public function getPackageRepositories()
    {
        $result = [];

        // Add standard package repositories.
        $result[] = ['type' => 'composer', 'url' => Constants::PACKAGIST_URL];

        // Add package repositories.
        $packages = $this->getPackages();
        foreach ($packages as $signature => $details) {
            if ($details !== null) {
                $result[] = ['type' => 'vcs', 'url' => $details];
            }
        }

        return $result;
    }

    /**
     * Get package requires.
     *
     * @return array
     */
    public function getPackageRequires()
    {
        $result = [];

        // Add package requires.
        $signatures = $this->getPackageSignatures();
        foreach ($signatures as $signature) {
            $result[$signature] = '*';
        }

        return $result;
    }

    /**
     * Is valid.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->exist() && !empty($this->get('name')) && !empty($this->get('homepage'));
    }

    /**
     * Validate.
     */
    public function validate()
    {
        if (!$this->isValid()) {
            Message::error(Constants::TITLE . ' not initialized. Run command init.');
        }
    }
}