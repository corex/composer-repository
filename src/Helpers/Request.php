<?php

namespace CoRex\Composer\Repository\Helpers;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    /**
     * Get view.
     *
     * @param string $default
     * @return mixed
     */
    public function getController($default = 'index')
    {
        return $this->get('controller', $default);
    }
}