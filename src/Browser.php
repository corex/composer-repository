<?php

namespace CoRex\Composer\Repository;

use CoRex\Composer\Repository\Browser\Template;
use CoRex\Filesystem\File;
use CoRex\Support\System\Input;
use CoRex\Support\System\Session;

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
        $this->template->set('content', $pageContent);
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