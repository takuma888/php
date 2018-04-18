<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;


use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionEdit extends UserController
{
    public function execGet()
    {
        $request = $this->getRequest();
        $session = $this->getSession();
        $gets = $request->query;
        $id = $gets->get('id');
        try {
            $user = $this->tcgCMF()
                ->providerUser()
                ->oneById($id);
            if (!$user) {
                throw new \Exception("用户不存在");
            }

            // 用户的角色
            $rootRole = $this->tcgCMF()
                ->dbMain()
                ->tblRoles()
                ->getRootNode();
            $rootRole = $this->tcgCMF()
                ->dbMain()
                ->tblRoles()
                ->model($rootRole);
            $roles = $rootRole->getSubTree();

            $userRoles = [];
            $user2roles = $this->tcgCMF()
                ->dbMain()
                ->tblUser2Role()
                ->all(function (QueryBuilder $queryBuilder) use ($id) {
                    $queryBuilder->where($queryBuilder->expr()->eq('`user_id`', ':user_id'))->setParameter(':user_id', $id);
                });
            foreach ($user2roles as $user2role) {
                $userRoles[] = $user2role['role_id'];
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'roles' => $roles,
                'user_roles' => $userRoles,
            ]);
        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
            return $this->redirect('dashboard.user.index');
        }
    }


    public function execPost()
    {
        $request = $this->getRequest();
        $session = $this->getSession();
        $gets = $request->query;
        $posts = $request->request;
        $id = $gets->get('id');

        try {
            $user = $this->tcgCMF()
                ->providerUser()
                ->oneById($id);
            if (!$user) {
                throw new \Exception("用户不存在");
            }

            $roles = $posts->get('roles', []);

            $this->tcgCMF()
                ->dbMain()
                ->transaction((function () use ($user, $roles) {
                    $this->tcgCMF()
                        ->serviceRole()
                        ->updateUserAdminRole($user, array_values($roles));
                })->bindTo($this));
        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
        }
        return $this->redirect('dashboard.user.index');
    }
}