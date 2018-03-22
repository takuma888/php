<?php


namespace Tachigo\Module\UI;

use TCG\Bundle\UI\TCGUIBundle;
use TCG\Component\Kernel\Module;

class TachigoUIModule extends Module
{

    public function getBundles()
    {
        return [
            new TCGUIBundle(),
        ];
    }


    public function getParent()
    {
        return [
            'TCGBaseModule',
            'TCGWebModule',
        ];
    }
}