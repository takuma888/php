<?php

namespace TCG\Module\CMS\Admin\Controller\DefaultController;

use TCG\Module\CMS\Admin\Controller\CMSAdminExec;

class ActionLogin extends CMSAdminExec
{
    public function execGet()
    {
        return $this->render('default/login.html.twig');
    }

    protected function needAuthenticate()
    {
        return false;
    }
}