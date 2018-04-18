<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;

use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionAdd extends RoleController
{
    public function exec()
    {
        $request = $this->getRequest();
        $id = $request->query->get('id', 0);
        if (!$id) {
            $rootRole = $this->tcgCMF()
                ->serviceRole()
                ->getRoot();
        } else {
            $rootRole = $this->tcgCMF()
                ->serviceRole()
                ->getNode($id);
        }

        if ($request->getMethod() == "POST") {
            $posts = $request->request;

            $key = trim($posts->get('key'));
            $name = trim($posts->get('name'));
            $description = trim($posts->get('description'));

            $session = $request->getSession();

            try {
                $this->tcgCMF()
                    ->dbMain()
                    ->transaction(function () use ($key, $name, $description, $rootRole) {
                        $role = $this->tcgCMF()
                            ->serviceRole()
                            ->create($key, 'admin', $rootRole);
                        $role->name = $name;
                        $role->description = $description;
                        $role->update();
                    });
                $session->getFlashBag()->add('success', '操作成功');
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
            }

            return $this->redirect('dashboard.role.index', [
                'id' => $rootRole->id,
            ]);

        }

        return $this->render('role/add.html.twig', [
            'root' => $rootRole,
        ]);
    }
}