<?php

namespace TCG\Module\CMS\Dashboard\Controller;

abstract class UserController extends CMSDashboardExec
{
    public function buildBreadcrumbs()
    {
        return parent::buildBreadcrumbs()
            ->tailBreadcrumbs('dashboard.user.index');
    }
}