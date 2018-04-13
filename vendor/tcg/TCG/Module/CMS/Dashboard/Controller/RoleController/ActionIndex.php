<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;


use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionIndex extends RoleController
{
    public function exec()
    {

        $rootRole = $this->tcgCMF()
            ->serviceRole()
            ->getRoot();
        $roleTree = $this->tcgCMF()->serviceRole()
            ->roleTree($rootRole);

        $roles = $roleTree->getRoot()->children;
        return $this->render('role/index.html.twig', [
            'roles' => $roles,
        ]);
    }
}