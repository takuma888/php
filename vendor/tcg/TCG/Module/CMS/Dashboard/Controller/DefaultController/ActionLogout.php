<?php

namespace TCG\Module\CMS\Dashboard\Controller\DefaultController;


use TCG\Module\CMS\Dashboard\Controller\DefaultController;

class ActionLogout extends DefaultController
{
    public function exec()
    {
        $this->tcgCMF()
            ->servicePassport()
            ->logout();
        return $this->redirect('dashboard_login');
    }

    protected function needAuthenticate()
    {
        return false;
    }
}