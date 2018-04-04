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
    const EXEC_CMD = 'Cmd';

    public function getBundles()
    {
        if ($this->execRoot != self::EXEC_CMD) {
            return [
                new TCGCMFBundle(),
            ];
        } else {
            return [];
        }
    }


    public function getParent()
    {
        if ($this->execRoot != self::EXEC_CMD) {
            return [
                'TCGWebModule'
            ];
        } else {
            return [
                'TCGConsoleModule'
            ];
        }
    }
}