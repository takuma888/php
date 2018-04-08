<?php


namespace Tachigo\Module\UI;

use TCG\Component\Kernel\Module;

class TachigoUIModule extends Module
{

    public function getBundles()
    {
        return [
            'TCGUIBundle',
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