<?php


namespace TCG\Module\CMS;


use TCG\Component\Kernel\Module;

class TCGCMSModule extends Module
{

    const EXEC_ADMIN = 'Admin';
    const EXEC_DASHBOARD = 'Dashboard';
    const EXEC_USER = 'User';
    const EXEC_CMD = 'Cmd';

    public function getBundles()
    {
        return [
            'TCGCMFBundle',
            'TCGTUIBundle'
        ];
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