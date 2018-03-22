<?php

namespace TCG\Module\Base;

use TCG\Bundle\Base\TCGBaseBundle;
use TCG\Component\Kernel\Module;

class TCGBaseModule extends Module
{

    public function getBundles()
    {
        return [
            new TCGBaseBundle(),
        ];
    }
}