<?php

namespace TCG\Module\CMS\Dashboard\Controller\DefaultController;


use TCG\Module\CMS\Dashboard\Controller\DefaultController;

class ActionLogout extends DefaultController
{
    public function exec()
    {
        $session = $this->getRequest()->getSession();
        $session->remove('uid');
        return $this->redirect('dashboard_login');
    }
}