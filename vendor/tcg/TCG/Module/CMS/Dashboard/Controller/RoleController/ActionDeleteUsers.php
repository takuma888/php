<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;


use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\CMSException;
use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionDeleteUsers extends RoleController
{
    public function exec()
    {
        $request = $this->getRequest();
        $gets = $request->query;
        $session = $request->getSession();
        $userIds = $gets->get('user_ids');
        $id = $gets->get('id');

        $role = $this->tcgCMF()
            ->providerRole()
            ->oneById($id);
        if (!$role) {
            throw new CMSException("角色不存在");
        }

        $users = $this->tcgCMF()
            ->dbMain()
            ->tblUsers()
            ->all(function (QueryBuilder $queryBuilder) use ($userIds) {
                if ($userIds) {
                    $queryBuilder->where($queryBuilder->expr()->in('`id`', $userIds));
                } else {
                    $queryBuilder->where('false');
                }
            });

        if ($request->getMethod() == 'POST') {
            try {
                $this->tcgCMF()
                    ->dbMain()
                    ->tblUser2Role()
                    ->delete(function (QueryBuilder $queryBuilder) use ($id, $userIds) {
                        $queryBuilder->andWhere($queryBuilder->expr()->eq('`role_id`', ':role_id'))->setParameter(':role_id', $id);
                        if ($userIds) {
                            $queryBuilder->andWhere($queryBuilder->expr()->in('`user_id`', $userIds));
                        } else {
                            $queryBuilder->andWhere('false');
                        }
                    });
                $session->getFlashBag()->add('success', '操作成功');
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
            }
            return $this->redirect('dashboard.role.index', [
                'id' => $role->id,
            ]);
        }

        return $this->render('role/delete-users.html.twig', [
            'users' => $users,
            'user_ids' => $userIds,
            'role' => $role,
        ]);
    }
}