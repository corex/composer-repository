<?php

// Set breadcrumbs.
use CoRex\Composer\Repository\Browser\Breadcrumbs;
use CoRex\Composer\Repository\Config;

Breadcrumbs::clear();
Breadcrumbs::add('Location', ['page' => 'location']);

$homepage = Config::load()->getHomepage();
$repositories = [
    'repositories' => [
        [
            'type' => 'composer',
            'url' => $homepage
        ]
    ]
];
$location = json_encode($repositories, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);
$location = str_replace("\n", '<br>', $location);
$location = str_replace(' ', '&nbsp;', $location);

?>
<?= Breadcrumbs::render() ?>
<pre><?= $location ?></pre>
