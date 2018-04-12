<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;

use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionAdd extends UserController
{
    public function exec()
    {

        return $this->render('user/add.html.twig');
    }
}