<?php

namespace CoRex\Composer\Repository\Controllers;

use CoRex\Composer\Repository\Base\BaseController;
use CoRex\Composer\Repository\Browser\Breadcrumbs;
use CoRex\Composer\Repository\Config;

class LocationController extends BaseController
{
    /**
     * Render.
     *
     * @return \CoRex\Template\Helpers\Engine
     * @throws \Exception
     */
    public function render()
    {
        $view = $this->view('location');
        $view->variable('title', 'Location');

        Breadcrumbs::clear();
        Breadcrumbs::add('Location', ['controller' => 'location']);

        // Compile location.
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
        $view->variable('location', $location);

        return $view;
    }
}