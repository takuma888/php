<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;

use TCG\Module\CMS\CMSException;
use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionPassword extends UserController
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

            return $this->render('user/password.html.twig', [
                'user' => $user,
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

            $password = trim($posts->get('password'));
            $confirmPassword = trim($posts->get('confirm_password'));

            if ($password != $confirmPassword) {
                throw new CMSException("两次密码不一致");
            }

            $this->tcgCMF()
                ->providerUser()
                ->update($user, [
                    'password' => md5($password)
                ]);

        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', $e->getMessage());
        }
        return $this->redirect('dashboard.user.index');
    }
}