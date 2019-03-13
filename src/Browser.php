<?php

namespace CoRex\Composer\Repository;

use CoRex\Composer\Repository\Browser\Breadcrumbs;
use CoRex\Composer\Repository\Browser\Url;
use CoRex\Composer\Repository\Controllers\LocationController;
use CoRex\Composer\Repository\Controllers\PackageController;
use CoRex\Composer\Repository\Controllers\PackagesController;
use CoRex\Composer\Repository\Controllers\ServicesController;
use CoRex\Composer\Repository\Exceptions\SiteException;
use CoRex\Composer\Repository\Helpers\Path;
use CoRex\Composer\Repository\Helpers\Request;
use CoRex\Composer\Repository\Helpers\Theme;
use CoRex\Filesystem\File;
use CoRex\Session\Session;
use CoRex\Site\Config as SiteConfig;
use CoRex\Site\Layout;

class Browser
{
    const DEFAULT_CONTROLLER = 'packages';

    /** @var Config */
    private $config;

    /** @var Layout */
    private $layout;

    /**
     * WebBrowser.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->config = Config::load();

        // Initialize theme.
        SiteConfig::setLayoutPath(Path::packageCurrent(['templates', 'layouts']));
        SiteConfig::setViewPath(Path::packageCurrent(['templates', 'views']));

        // Set theme.
        $theme = Config::load()->getTheme();
        $themeConstant = Theme::details($theme);
        SiteConfig::setTheme($themeConstant);

        $this->layout = Layout::load('site');
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
     *
     * @throws \Exception
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
     *
     * @throws \Exception
     */
    public function dispatch()
    {
        Session::initialize();
        $request = Request::createFromGlobals();

        if ($request->getController(self::DEFAULT_CONTROLLER) == self::DEFAULT_CONTROLLER) {
            Breadcrumbs::clear();
            Breadcrumbs::add('Packages', []);
        }


        $view = $this->executeController($request);

        $this->layout->variables([
            'url-home' => Url::home(),
            'url-packagist' => Url::packagist(),
            'url-order' => Url::build(['controller' => 'services', 'service' => 'order']),
            'url-order-status' => Url::build(['controller' => 'services', 'service' => 'getOrderStatus']),
            'title' => $this->config->getName(),
            'breadcrumbs' => Breadcrumbs::render(),
            'body' => $view
        ]);
    }

    /**
     * Display.
     */
    public function display()
    {
        print($this->layout);
    }

    /**
     * Execute controller.
     *
     * @param Request $request
     * @param string $defaultController
     * @return \CoRex\Site\View|null
     * @throws \Exception
     */
    private function executeController(Request $request, $defaultController = self::DEFAULT_CONTROLLER)
    {
        // Register controllers.
        $controllers = [
            'package' => PackageController::class,
            'packages' => PackagesController::class,
            'location' => LocationController::class,
            'services' => ServicesController::class
        ];

        $controllerName = $request->getController($defaultController);
        $view = null;
        $controller = null;

        // Execute controller.
        if (isset($controllers[$controllerName])) {
            $controllerClass = $controllers[$controllerName];
            $controller = new $controllerClass($request);
        } else {
            throw new SiteException('Controller not found.');
        }

        if ($controller !== null) {
            try {
                $view = call_user_func([$controller, 'render']);
            } catch (\Exception $exception) {
                error_log($exception->getMessage());
                if ($exception instanceof SiteException) {
                    $this->layout->variable('error', $exception->getMessage());
                }
            }
        }

        return $view;
    }
}