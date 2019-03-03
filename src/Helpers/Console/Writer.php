<?php

namespace CoRex\Composer\Repository\Helpers\Console;

use League\CLImate\Util\Writer\WriterInterface;

class Writer implements WriterInterface
{
    /**
     * @param  string $content
     *
     * @return void
     */
    public function write($content)
    {
        // Do nothing. Purpose is hiding output.
    }
}