<?php

use CoRex\Composer\Repository\Browser\Breadcrumbs;
use CoRex\Composer\Repository\Browser\Element;
use CoRex\Composer\Repository\Browser\Url;
use CoRex\Composer\Repository\Helpers\Tabs;
use CoRex\Composer\Repository\Services\PackageService;
use CoRex\Support\System\Input;

$signature = Input::getQuery('signature');
$version = Input::getQuery('version');
$action = Input::getQuery('action', 'details');
$filename = Input::getQuery('filename');

$packageService = PackageService::load($signature);
if ($version === null) {
    $version = $packageService->getLatestVersion();
}

if ($packageService->exists()) {
    $packageVersion = $packageService->getVersionEntity($version);

    $versions = $packageService->getVersions(true);

    // Prepare requires.
    $requiresSignatures = array_keys($packageVersion->getRequires());
    $requires = [];
    foreach ($requiresSignatures as $requiresSignature) {
        $requires[] = Url::link($requiresSignature, ['page' => 'package', 'signature' => $requiresSignature]);
    }

    $homepage = $packageVersion->getValue('homepage');
    $homepage = '<a href="' . $homepage . '" target="_blank">' . $homepage . '</a>';

    $propertiesTable = [
        'Signature' => $signature,
        'Version' => $version,
        'Description' => $packageVersion->getValue('description'),
        'Type' => $packageVersion->getValue('type', 'library'),
        'Source' => $packageVersion->getValue('source.url'),
        'Require' => implode('<br>', $requires),
        'Homepage' => $homepage,
        'Keywords' => implode(', ', $packageVersion->getValue('keywords', []))
    ];
}

// Set breadcrumbs.
Breadcrumbs::removeFrom($signature);
Breadcrumbs::add($signature, ['page' => 'package', 'signature' => $signature]);

// Base params.
$baseParams = ['page' => 'package', 'signature' => $signature, 'version' => $version];

// Tabs.
$tabs = [];
$allowedTabs = Tabs::getSignatureAllowed($signature);
if (in_array('details', $allowedTabs)) {
	$tabs[] = Element::button('Details', $action == 'details', $baseParams + ['action' => 'details']);
}
if (in_array('readme', $allowedTabs)) {
	$tabs[] = Element::button('Readme', $action == 'readme', $baseParams + ['action' => 'readme']);
}
if (in_array('changelog', $allowedTabs)) {
	$tabs[] = Element::button('Changelog', $action == 'changelog', $baseParams + ['action' => 'changelog']);
}
if (in_array('composer', $allowedTabs)) {
	$tabs[] = Element::button('Composer', $action == 'composer', $baseParams + ['action' => 'composer']);
}
if (in_array('files', $allowedTabs)) {
	$tabs[] = Element::button('Files', $action == 'files', $baseParams + ['action' => 'files']);
}

?>
<?= Breadcrumbs::render() ?>

<?php if ($packageService->exists()): ?>

	<?= Element::dropdown($versions, $version, $baseParams, true, 10) ?>&nbsp;
    <?= ' ' . implode(' ', $tabs) ?>
	<br><br>

	<!-- Details -->
    <?php if ($action == 'details' && in_array('details', $allowedTabs)): ?>
        <?= Element::propertiesTable($propertiesTable) ?>
    <?php endif; ?>

	<!-- readme -->
    <?php if ($action == 'readme' && in_array('readme', $allowedTabs)): ?>
        <?php
        $content = $packageVersion->getMap()->getReadmeContent();
        try {
            if (!empty($content)) {
                $markdown = new \cebe\markdown\GithubMarkdown();
                $content = $markdown->parse($content);
                print($content);
            } else {
                Element::error('No readme found in ' . $signature . '.');
            }
        } catch (Exception $e) {
            return Element::error($e->getMessage());
        }
        ?>
    <?php endif; ?>

	<!-- changelog -->
    <?php if ($action == 'changelog' && in_array('changelog', $allowedTabs)): ?>
        <?php
        $content = $packageVersion->getMap()->getChangelogContent();
        try {
            if (!empty($content)) {
                $markdown = new \cebe\markdown\GithubMarkdown();
                $content = $markdown->parse($content);
                print($content);
            } else {
                Element::error('No readme found in ' . $signature . '.');
            }
        } catch (Exception $e) {
            return Element::error($e->getMessage());
        }
        ?>
    <?php endif; ?>

	<!-- composer.json -->
    <?php if ($action == 'composer' && in_array('composer', $allowedTabs)): ?>
        <?php
        $content = trim($packageVersion->getComposerJson());
        print(Element::code($content));
        ?>
    <?php endif; ?>

	<!-- files -->
    <?php if ($action == 'files' && in_array('files', $allowedTabs)): ?>
        <?php
        $filenames = $packageVersion->getArchive()->getArchiveFilenames();
        foreach ($filenames as $filename) {
            $url = Url::link($filename, $baseParams + ['action' => 'fileshow', 'filename' => urlencode($filename)]);
            print($url . '<br>');
        }
        ?>
    <?php endif; ?>

	<!-- fileshow -->
    <?php if ($action == 'fileshow' && in_array('files', $allowedTabs)): ?>
        <?php
        $filename = urldecode($filename);
        print('<h4>' . $filename . '</h4>');
        $content = $packageVersion->getArchive()->getArchiveContent($filename);
        print(Element::code($content));
        ?>
    <?php endif; ?>

<?php else: ?>
    <?= Element::error('Package ' . $signature . ' not found.') ?>
<?php endif; ?>

<script