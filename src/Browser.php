<?php

namespace CoRex\Composer\Repository;

use CoRex\Composer\Repository\Browser\Template;
use CoRex\Composer\Repository\Helpers\Input;
use CoRex\Composer\Repository\Helpers\Path;
use CoRex\Filesystem\File;
use CoRex\Session\Session;

class Browser
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Template
     */
    private $template;

    private $pagePath;

    /**
     * WebBrowser constructor.
     */
    public function __construct()
    {
        $this->config = Config::load();
        $this->template = Template::load('standard');
        $this->pagePath = Path::packageCurrent(['pages']);
    }

    /**
     * Create browser files.
     */
    public static function createBrowserFiles()
    {
        $config = Config::load();
        $vendorDirectory = Path::packages();

        // Create ".htaccess".
        $lines = [
            '<IfModule mod_rewrite.c>',
            'RewriteEngine On',
            'RewriteBase /',
            'RewriteRule ^index\.php$ - [L]',
            'RewriteCond %{REQUEST_FILENAME} !-f',
            'RewriteCond %{REQUEST_FILENAME} !-d',
            'RewriteRule . /index.php [L]',
            '</IfModule>'
        ];
        File::putLines($config->getPath(['.htaccess']), $lines);

        // Create "index.php".
        $lines = [
            '<' . '?php',
            'require_once(\'' . $vendorDirectory . '/autoload.php' . '\');',
            '\\' . Browser::class . '::run();'
        ];
        File::putLines($config->getPath(['index.php']), $lines);

        // Copy stylesheet from "scrivo/highlight.php".
        $stylesheet = 'github-gist.css';
        $filename = Path::packages(['scrivo', 'highlight.php', 'styles', $stylesheet]);
        File::copy($filename, Config::load()->getPath());
    }

    /**
     * Run.
     */
    public static function run()
    {
        // Stop code on Google Chrome's stupid "/favicon.ico" lookup.
        if ($_SERVER['REQUEST_URI'] == '/favicon.ico') {
            exit;
        }

        $webBrowser = new static();
        $webBrowser->dispatch();
        $webBrowser->display();
    }

    /**
     * Dispatch.
     */
    public function dispatch()
    {
        Session::initialize();
        $this->template->set('title', $this->config->getName());
        $this->template->set('homeUrl', $this->config->getHomepage());
        $page = Input::getQuery('page', 'index');
        $pageContent = $this->getPage($page);
        if ($page != 'services') {
            $this->template->set('content', $pageContent);
        } else {
            $response = $this->getPage('services');
            print($response);
            exit;
        }
    }

    /**
     * Display.
     */
    public function display()
    {
        print($this->template);
    }

    /**
     * Get page.
     *
     * @param string $page
     * @return string
     */
    private function getPage($page)
    {
        $filename = $this->pagePath . '/' . $page . '.php';
        if (!File::exist($filename)) {
            $filename = $this->pagePath . '/index.php';
            $this->template->set('content', 'Page ' . $page . ' not found.');
        }

        ob_start();
        require($filename);
        return ob_get_clean();
    }
}