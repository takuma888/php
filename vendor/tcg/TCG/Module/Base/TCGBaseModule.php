<?php

namespace TCG\Module\Base;

use TCG\Component\Kernel\Module;

class TCGBaseModule extends Module
{

    public function getBundles()
    {
        return [
            'TCGBaseBundle',
        ];
    }
}