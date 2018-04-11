<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;


use TCG\Module\CMS\Dashboard\Controller\CMSDashboardExec;

class ActionIndex extends CMSDashboardExec
{
    public function exec()
    {
        return $this->render('user/index.html.twig');
    }

    public function buildBreadcrumbs()
    {
        return parent::buildBreadcrumbs()
            ->tailBreadcrumbs('dashboard.user.index');
    }
}