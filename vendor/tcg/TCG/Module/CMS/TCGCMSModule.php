<?php


namespace TCG\Module\CMS;


use TCG\Bundle\CMF\TCGCMFBundle;
use TCG\Component\Kernel\Module;

class TCGCMSModule extends Module
{
    public function getBundles()
    {
        return [
            new TCGCMFBundle(),
        ];
    }


    public function getParent()
    {
        return [
            'TCGWebModule'
        ];
    }
}