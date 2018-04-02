<?php

namespace TCG\Module\Console\Component;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct($name = 'UNKNOWN', $version = 'PHP v' . PHP_VERSION)
    {
        parent::__construct($name, $version);
    }
}