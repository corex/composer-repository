<?php

namespace CoRex\Composer\Repository\Controllers;

use CoRex\Composer\Repository\Base\BaseController;
use CoRex\Composer\Repository\Browser\Breadcrumbs;
use CoRex\Composer\Repository\Browser\Url;
use CoRex\Composer\Repository\Helpers\Tabs;
use CoRex\Composer\Repository\Services\PackageService;
use Highlight\Highlighter;

class PackageController extends BaseController
{
    /**
     * Render.
     *
     * @return \CoRex\Template\Helpers\Engine
     * @throws \Exception
     */
    public function render()
    {
        $signature = $this->get('signature');
        $version = $this->get('version');
        $action = $this->get('action', 'details');
        $filename = $this->get('filename');

        // Set breadcrumbs.
        Breadcrumbs::removeFrom($signature);
        Breadcrumbs::add($signature, ['controller' => 'package', 'signature' => $signature]);

        $view = $this->view('package');
        $view->variable('title', 'Package ' . $signature);

        $packageService = PackageService::load($signature);
        if ($packageService->exists()) {
            if ($version === null) {
                $version = $packageService->getLatestVersion();
            }
            $packageVersion = $packageService->getVersionEntity($version);

            $versions = $packageService->getVersions(true);
            $versionsDropdown = [];
            foreach ($versions as $versionDropdown) {
                $versionsDropdown[] = [
                    'version' => $versionDropdown,
                    'url' => Url::build([
                        'controller' => 'package',
                        'signature' => $signature,
                        'version' => $versionDropdown,
                        'action' => $action
                    ])
                ];
            }

            // Prepare requires.
            $requiresSignatures = array_keys($packageVersion->getRequires());
            $requires = [];
            foreach ($requiresSignatures as $requiresSignature) {
                $requires[] = Url::link($requiresSignature,
                    ['controller' => 'package', 'signature' => $requiresSignature]);
            }

            $homepage = $packageVersion->getValue('homepage');
            $homepage = '<a href="' . $homepage . '" target="_blank">' . $homepage . '</a>';
            $baseParams = ['controller' => 'package', 'signature' => $signature, 'version' => $version];

            // Details.
            $properties = [];
            $properties[] = ['title' => 'Signature', 'value' => $signature];
            $properties[] = ['title' => 'Version', 'value' => $version];
            $properties[] = ['title' => 'Description', 'value' => $packageVersion->getValue('description')];
            $properties[] = ['title' => 'Type', 'value' => $packageVersion->getValue('type', 'library')];
            $properties[] = ['title' => 'Source', 'value' => $packageVersion->getValue('source.url')];
            $properties[] = ['title' => 'Require', 'value' => implode('<br>', $requires),];
            $properties[] = ['title' => 'Homepage', 'value' => $homepage];
            $properties[] = [
                'title' => 'Keywords',
                'value' => implode(', ', $packageVersion->getValue('keywords', []))
            ];
            $view->variable('content-details', $properties);

            // Readme.
            $content = $packageVersion->getMap()->getReadmeContent();
            try {
                if (!empty($content)) {
                    $markdown = new \cebe\markdown\GithubMarkdown();
                    $content = $markdown->parse($content);
                } else {
                    $content = 'No readme found in ' . $signature . '.';
                }
            } catch (\Exception $e) {
                $content = $e->getMessage();
            }
            $view->variable('content-readme', $content);

            // Changelog.
            if ($action == 'changelog') {
                $content = $packageVersion->getMap()->getChangelogContent();
                try {
                    if (!empty($content)) {
                        $markdown = new \cebe\markdown\GithubMarkdown();
                        $content = $markdown->parse($content);
                    } else {
                        $content = 'No readme found in ' . $signature . '.';
                    }
                } catch (\Exception $e) {
                    $content = $e->getMessage();
                }
                $view->variable('content-changelog', $content);
            }

            // Create highlighter.
            $highlighter = new Highlighter();
            $highlighter->setAutodetectLanguages(['markdown', 'php', 'javascript', 'json']);

            // Composer.
            if ($action == 'composer') {
                $content = trim($packageVersion->getComposerJson());
                try {
                    $highlighted = $highlighter->highlightAuto($content);
                    $content = "<pre class=\"hljs {$highlighted->language}\">\n";
                    $content .= $highlighted->value . "\n";
                    $content .= "</pre>\n";
                } catch (\Exception $e) {
                    $content = $e->getMessage();
                }
                $view->variable('content-composer', $content);
            }

            // Files.
            if ($action == 'files') {
                $filenames = $packageVersion->getArchive()->getArchiveFilenames();
                $filenameUrls = [];
                foreach ($filenames as $filenameUrl) {
                    $filenameUrls[] = [
                        'url' => Url::link(
                            $filenameUrl,
                            $baseParams + ['action' => 'fileshow', 'filename' => urlencode($filenameUrl)]
                        )
                    ];
                }
                $view->variable('content-files', $filenameUrls);
            }

            // Fileshow.
            if ($action == 'fileshow') {
                $filename = urldecode($filename);
                $content = $packageVersion->getArchive()->getArchiveContent($filename);
                $highlighted = $highlighter->highlightAuto($content);
                $content = "<pre class=\"hljs {$highlighted->language}\">\n";
                $content .= $highlighted->value . "\n";
                $content .= "</pre>\n";
                $view->variable('content-fileshow', [
                    'filename' => $filename,
                    'content' => $content
                ]);
            }

            // Set tab urls.
            $allowedTabs = Tabs::getSignatureAllowed($signature);
            $tabUrls = [];
            if (in_array('details', $allowedTabs)) {
                $tabUrls['url-details'] = Url::build($baseParams + ['action' => 'details']);
            }
            if (in_array('readme', $allowedTabs)) {
                $tabUrls['url-readme'] = Url::build($baseParams + ['action' => 'readme']);
            }
            if (in_array('changelog', $allowedTabs)) {
                $tabUrls['url-changelog'] = Url::build($baseParams + ['action' => 'changelog']);
            }
            if (in_array('composer', $allowedTabs)) {
                $tabUrls['url-composer'] = Url::build($baseParams + ['action' => 'composer']);
            }
            if (in_array('files', $allowedTabs)) {
                $tabUrls['url-files'] = Url::build($baseParams + ['action' => 'files']);
            }
            $view->variable('tab', $tabUrls);

            // Set active.
            $active = [
                'details' => $action == 'details',
                'readme' => $action == 'readme',
                'changelog' => $action == 'changelog',
                'composer' => $action == 'composer',
                'files' => $action == 'files',
                'fileshow' => $action == 'fileshow'
            ];
            $view->variable('active', $active);

            $view->variable('version', $version);
            $view->variable('versions', $versionsDropdown);
        }

        return $view;
    }
}