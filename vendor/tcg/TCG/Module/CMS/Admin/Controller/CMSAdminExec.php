<?php

namespace TCG\Module\CMS\Admin\Controller;

use TCG\Bundle\CMF\PublicTrait as CMFTrait;
use TCG\Module\CMS\CMSExec;

abstract class CMSAdminExec extends CMSExec
{
    use CMFTrait;


    public function __invoke()
    {
        $this->buildBreadcrumbs();
        return parent::__invoke(); // TODO: Change the autogenerated stub
    }

    protected function authenticate()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $userId = $session->get('uid');
        if (!$userId) {
            return $this->redirect('admin_login');
        }
        // 生成account
        $user = $this->tcgCMF()
            ->providerUser()
            ->oneById($userId);
        if (!$user) {
            return $this->redirect('admin_login');
        }

        $account = $this->tcgCMF()
            ->serviceUser()
            ->account($user);
        $this->setAccount($account);
    }


    public function buildBreadcrumbs()
    {
        return $this;
    }
}