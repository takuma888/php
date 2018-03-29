<?php


namespace TCG\Module\CMS;


use TCG\Bundle\CMF\TCGCMFBundle;
use TCG\Bundle\UI\TCGUIBundle;
use TCG\Component\Kernel\Module;

class TCGCMSModule extends Module
{

    const EXEC_ADMIN = 'Admin';
    const EXEC_DASHBOARD = 'Dashboard';
    const EXEC_USER = 'User';

    public function getBundles()
    {
        return [
            new TCGUIBundle(),
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