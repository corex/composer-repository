<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Helpers\Traits\DataTrait;

class ComposerJson
{
    use DataTrait;

    /**
     * ComposerJson constructor.
     *
     * @param string $json
     */
    public function __construct($json)
    {
        $data = json_decode($json, true);
        if ($data === null) {
            $data = [];
        }
        $this->setArray($data);
    }

    /**
     * Get require.
     *
     * @return array
     */
    public function getRequire()
    {
        return $this->get('require', []);
    }
}