<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;

use TCG\Module\CMS\CMSException;
use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionPermission extends RoleController
{
    public function exec()
    {
        $request = $this->getRequest();
        $gets = $request->query;
        $posts = $request->request;
        $session = $request->getSession();
        $id = $gets->get('id');
        $role = $this->tcgCMF()
            ->providerRole()
            ->oneById($id);
        if (!$role) {
            throw new CMSException("角色不存在");
        }

        $permissions = $posts->get('permissions', []);

        try {
            $this->tcgCMF()
                ->dbMain()
                ->transaction((function () use ($role, $permissions) {
                    $this->tcgCMF()
                        ->serviceRole()
                        ->updateRoleAdminPermissions($role, $permissions);
                })->bindTo($this));
            $session->getFlashBag()->add('success', '操作成功');
        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
        }
        return $this->redirect('dashboard.role.index', [
            'id' => $role->id,
        ]);
    }
}