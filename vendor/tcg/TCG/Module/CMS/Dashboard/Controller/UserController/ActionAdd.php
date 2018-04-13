<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;

use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\CMSException;
use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionAdd extends UserController
{
    public function exec()
    {
        $request = $this->getRequest();

        // 获取所有的后台角色列表
        $roles = $this->tcgCMF()
            ->dbMain()
            ->tblRoles()
            ->all(function (QueryBuilder $queryBuilder) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('`type`', ':type'))->setParameter(':type', 'admin');
            });
        if ($request->getMethod() == 'POST') {
            $session = $request->getSession();
            $posts = $request->request;

            $username = trim($posts->get('username'));
            $email = trim($posts->get('email'));
            $mobile = trim($posts->get('mobile'));
            $name = trim($posts->get('name'));
            $qq = trim($posts->get('qq'));
            $wechat = trim($posts->get('wechat'));
            $password = trim($posts->get('password'));
            $confirm_password = trim($posts->get('confirm_password'));

            $roleIds = $posts->get('roles', []);
            try {

                if ($password != $confirm_password) {
                    throw new CMSException("密码和确认密码不一致");
                }

                $transaction = function () use ($username, $email, $mobile, $name, $qq, $wechat, $password, $roleIds) {
                    $user = $this->tcgCMF()
                        ->serviceUser()
                        ->create($username, $email, $mobile, $password);

                    $user->name = $name;
                    $user->qq = $qq;
                    $user->wechat = $wechat;
                    $user->update();

                    $this->tcgCMF()
                        ->serviceRole()
                        ->updateUserRole($user, $roleIds);
                };
                $transaction->bindTo($this);

                $this->tcgCMF()
                    ->dbMain()
                    ->master()
                    ->transaction($transaction);

                $session->getFlashBag()->add('success', '操作成功');
                return $this->redirect('dashboard.user.index');
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
                return $this->redirect('dashboard.user.add');
            }

        }
        return $this->render('user/add.html.twig', [
            'roles' => $roles,
        ]);
    }
}