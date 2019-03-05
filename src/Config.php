<?php

namespace CoRex\Composer\Repository;

use CoRex\Composer\Repository\Helpers\Console;
use CoRex\Composer\Repository\Helpers\Path;
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
     * Get theme.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->get('theme', Constants::THEME);
    }

    /**
     * Set theme.
     *
     * @param string $theme
     * @return $this
     */
    public function setTheme($theme)
    {
        $this->set('theme', $theme);
        return $this;
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
     * Get package name.
     *
     * @return string
     */
    public function getPackageName()
    {
        return $this->get('package-name', Constants::NAME);
    }

    /**
     * Set package name.
     *
     * @param string $name
     * @return $this
     */
    public function setPackageName($name)
    {
        $this->set('package-name', $name);
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
     * Get tab signatures.
     *
     * @return array
     */
    public function getTabSignatures()
    {
        $configTabs = $this->get('tabs', []);
        return array_keys($configTabs);
    }

    /**
     * Get signature tabs.
     *
     * @param string $signature
     * @return array
     */
    public function getSignatureTabs($signature)
    {
        $configTabs = $this->get('tabs', []);
        if (isset($configTabs[$signature])) {
            return $configTabs[$signature];
        }
        return [];
    }

    /**
     * Set signature tabs.
     *
     * @param string $signature
     * @param array $tabs
     * @return $this
     */
    public function setSignatureTabs($signature, array $tabs)
    {
        $configTabs = $this->get('tabs', []);
        if (count($tabs) > 0) {
            $configTabs[$signature] = $tabs;
        } else {
            if (array_key_exists($signature, $configTabs)) {
                unset($configTabs[$signature]);
            }
        }
        $this->set('tabs', $configTabs);
        return $this;
    }

    /**
     * Has signature tab.
     *
     * @param string $signature
     * @param string $tab
     * @return boolean
     */
    public function hasSignatureTab($signature, $tab)
    {
        return in_array($tab, $this->getSignatureTabs($signature));
    }

    /**
     * Is valid.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->exist() && !empty($this->get('name')) && !empty($this->get('package-name'))
            && !empty($this->get('homepage'));
    }

    /**
     * Validate.
     */
    public function validate()
    {
        if (!$this->isValid()) {
            Console::throwError(Constants::TITLE . ' not initialized. Run command init.');
        }
    }
}