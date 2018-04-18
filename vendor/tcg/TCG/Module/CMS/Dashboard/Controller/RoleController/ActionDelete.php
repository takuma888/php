<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;


use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionDelete extends RoleController
{

    public function exec()
    {
        $request = $this->getRequest();
        $gets = $request->query;
        $session = $request->getSession();
        $ids = $gets->get('ids');

        $roles = [];
        foreach ($ids as $id) {
            $role = $this->tcgCMF()
                ->providerRole()
                ->oneById($id);
            if ($role) {
                $roles[] = $role;
            }
        }

        if ($request->getMethod() == 'POST') {
            try {
                $this->tcgCMF()
                    ->dbMain()
                    ->transaction((function () use ($roles) {
                        foreach ($roles as $role) {
                            $this->tcgCMF()
                                ->providerRole()
                                ->remove($role);
                        }
                    })->bindTo($this));
                $session->getFlashBag()->add('success', '操作成功');
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
            }
            return $this->redirect('dashboard.role.index');
        }

        return $this->render('role/delete.html.twig', [
            'roles' => $roles,
            'ids' => $ids,
        ]);
    }
}