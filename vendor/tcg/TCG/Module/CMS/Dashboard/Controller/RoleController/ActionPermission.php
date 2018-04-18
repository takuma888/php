<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;

use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionPermission extends RoleController
{
    public function exec()
    {
        $request = $this->getRequest();
        $gets = $request->query;
        $posts = $request->request;

        pre($posts->all());
    }
}