<?php

namespace CoRex\Composer\Repository\Interfaces;

use CoRex\Site\View;

interface ControllerInterface
{
    /**
     * Render.
     *
     * @return View
     */
    public function render();
}