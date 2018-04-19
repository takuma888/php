<?php


namespace TCG\Module\CMS\Admin\Controller\DefaultController;


use TCG\Module\CMS\Admin\Controller\CMSAdminExec;

class ActionLogout extends CMSAdminExec
{
    public function exec()
    {
        $this->tcgCMF()
            ->servicePassport()
            ->logout();
        return $this->redirect('admin_login');
    }

    protected function needAuthenticate()
    {
        return false;
    }
}