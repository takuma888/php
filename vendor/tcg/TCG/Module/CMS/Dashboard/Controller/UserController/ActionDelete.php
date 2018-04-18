<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;


use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionDelete extends UserController
{
    public function execGet()
    {
        $request = $this->getRequest();
        $gets = $request->query;
        $ids = $gets->get('ids', []);


        $users = $this->tcgCMF()
            ->dbMain()
            ->tblUsers()
            ->all(function (QueryBuilder $queryBuilder) use ($ids) {
                if ($ids) {
                    $queryBuilder->where($queryBuilder->expr()->in('`id`', $ids));
                } else {
                    $queryBuilder->where('false');
                }
            });

        return $this->render('user/delete.html.twig', [
            'users' => $users,
            'ids' => $ids,
        ]);
    }


    public function execPost()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $gets = $request->query;
        $ids = $gets->get('ids', []);

        try {
            $this->tcgCMF()
                ->dbMain()
                ->transaction((function () use ($ids) {
                    foreach ($ids as $id) {
                        $user = $this->tcgCMF()
                            ->providerUser()
                            ->oneById($id);
                        if ($user) {
                            $this->tcgCMF()
                                ->serviceRole()
                                ->updateUserAdminRole($user, []);
                        }
                    }
                })->bindTo($this));
            $session->getFlashBag()->add('success', '操作成功');
        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
        }
        return $this->redirect('dashboard.user.index');
    }
}