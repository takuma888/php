<?php

namespace TCG\Module\CMS\Dashboard\Controller;


abstract class RoleController extends CMSDashboardExec
{
    public function buildBreadcrumbs()
    {
        return parent::buildBreadcrumbs()
            ->tailBreadcrumbs('dashboard.role.index');
    }
}