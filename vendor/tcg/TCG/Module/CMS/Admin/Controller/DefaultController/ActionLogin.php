<?php

namespace TCG\Module\CMS\Admin\Controller\DefaultController;

use TCG\Module\CMS\Admin\Controller\CMSAdminExec;

class ActionLogin extends CMSAdminExec
{
    public function exec()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "POST") {
            $posts = $request->request;
            $session = $request->getSession();
            try {
                $username = $posts->get('username');
                $password = $posts->get('password');
                $this->tcgCMF()
                    ->servicePassport()
                    ->loginByUsername($username, $password);
                return $this->redirect('admin_homepage');
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
                return $this->redirect('admin_login');
            }
        }
        return $this->render('default/login.html.twig');
    }

    protected function needAuthenticate()
    {
        return false;
    }
}