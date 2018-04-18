<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;


use TCG\Module\CMS\CMSException;
use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionEdit extends RoleController
{
    public function exec()
    {
        $request = $this->getRequest();
        $gets = $request->query;
        $id = $gets->get('id');

        $role = $this->tcgCMF()
            ->providerRole()
            ->oneById($id);
        if (!$role) {
            throw new CMSException("角色不存在");
        }

        if ($role->key == 'root' || $role->key == 'super_admin' || $role->key == 'developer') {
            throw new CMSException("该角色禁止进行修改");
        }

        if ($request->getMethod() == 'POST') {
            $posts = $request->request;

            $key = trim($posts->get('key'));
            $name = trim($posts->get('name'));
            $description = trim($posts->get('description'));
            $session = $request->getSession();

            try {
                if (empty($key)) {
                    throw new CMSException("角色KEY不能为空");
                }
                // 验证重复性
                $tmp = $this->tcgCMF()
                    ->providerRole()
                    ->oneBy('key', $key);
                if ($tmp && $tmp->id != $role->id) {
                    throw new CMSException("角色KEY重复");
                }

                $this->tcgCMF()
                    ->providerRole()
                    ->update($role, [
                        'key' => $key,
                        'name' => $name,
                        'description' => $description,
                    ]);
                $session->getFlashBag()->add('success', '操作成功');
                return $this->redirect('dashboard.role.index', [
                    'id' => $role->id,
                ]);
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
                return $this->redirect('dashboard.role.index', [
                    'id' => $role->id,
                ]);
            }


        }

        return $this->render('role/edit.html.twig', [
            'role' => $role,
        ]);
    }
}