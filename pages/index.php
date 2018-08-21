<?php

use CoRex\Composer\Repository\Browser\Breadcrumbs;
use CoRex\Composer\Repository\Browser\Element;
use CoRex\Composer\Repository\Browser\Url;
use CoRex\Composer\Repository\Config;
use CoRex\Composer\Repository\Services\PackagesService;

$signatures = Config::load()->getPackageSignatures();

Breadcrumbs::clear();
Breadcrumbs::add('Packages', []);

try {
    $packagesService = PackagesService::load();
    $packages = [];
    foreach ($signatures as $signature) {
        $packageService = $packagesService->getPackage($signature);
        $latestVersion = $packageService->getLatestVersion();
        $packageVersion = $packageService->getVersionEntity($latestVersion);
        $link = Url::link($signature, ['page' => 'package', 'signature' => $signature]);
        $packages[$link] = $packageVersion->getValue('description');
    }
} catch (\Exception $e) {
    print(Element::error('Not possible to get list of registered packages.'));
} ?>
<?= Breadcrumbs::render() ?>
<?= Element::title('Registered packages (signatures)') ?>
<?= Element::propertiesTable($packages) ?>
