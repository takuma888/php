<?php

namespace TCG\Module\CMS\Dashboard\Controller\DefaultController;


use TCG\Module\CMS\CMSException;
use TCG\Module\CMS\Dashboard\Controller\DefaultController;

class ActionLogin extends DefaultController
{
    public function exec()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "POST") {
            $posts = $request->request;
            $session = $request->getSession();
            try {
                $username = trim($posts->get('username'));
                if (!$username) {
                    throw new CMSException("用户名不能为空");
                }
                $password = trim($posts->get('password'));
                if (!$password) {
                    throw new CMSException("密码不能为空");
                }
                $user = $this->tcgCMF()
                    ->providerUser()
                    ->oneBy('username', $username);
                if (!$user) {
                    throw new CMSException("用户不存在");
                }
                if (!$this->tcgCMF()->serviceUser()->checkPassword($user, $password)) {
                    throw new CMSException("密码错误");
                }
                // 成功
                $session->set('uid', $user->id);
                return $this->redirect('dashboard_homepage');
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
                return $this->redirect('dashboard_login');
            }
        }
        return $this->render('default/login.html.twig');
    }

    protected function needAuthenticate()
    {
        return false;
    }
}