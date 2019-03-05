<?php

namespace CoRex\Composer\Repository\Controllers;

use CoRex\Composer\Repository\Base\BaseController;
use CoRex\Composer\Repository\Browser\Url;
use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Services\PackagesService;

class PackagesController extends BaseController
{
    /**
     * Render.
     *
     * @return \CoRex\Template\Helpers\Engine
     * @throws \Exception
     */
    public function render()
    {
        $view = $this->view('packages');
        $view->variable('title', 'Packages');

        // Compile list of packages.
        $packages = [];
        $message = null;
        try {
            $signatures = Config::load()->getPackageSignatures();
            $packagesService = PackagesService::load();
            foreach ($signatures as $signature) {
                $packageService = $packagesService->getPackage($signature);
                $latestVersion = $packageService->getLatestVersion();
                $packageVersion = $packageService->getVersionEntity($latestVersion);
                if ($packageVersion !== null) {
                    $url = Url::link($signature, ['controller' => 'package', 'signature' => $signature]);
                    $packages[] = [
                        'url' => $url,
                        'signature' => $signature,
                        'version' => $latestVersion,
                        'description' => $packageVersion->getValue('description')
                    ];
                }
            }
            if (count($packages) == 0) {
                $message = 'No packages registered.';
            }
        } catch (\Exception $e) {
            $packages = [];
            $message = 'Not possible to get list of registered packages.';
        }
        $view->variable('packages', $packages);
        if ($message !== null) {
            $view->variable('message', $message);
        }

        return $view;
    }
}