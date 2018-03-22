<?php


namespace Tachigo\Module\UI;

use Tachigo\Bundle\UI\TachigoUIBundle;
use TCG\Component\Kernel\Module;

class TachigoUIModule extends Module
{

    public function getBundles()
    {
        return [
            new TachigoUIBundle(),
        ];
    }


    public function getParent()
    {
        return [
            'TCGBaseModule',
            'TCGWebModule'
        ];
    }
}