<?php

namespace TCG\Module\CMS\Dashboard\Controller\DefaultController;


use TCG\Module\CMS\Dashboard\Controller\DefaultController;

class ActionIndex extends DefaultController
{
    public function exec()
    {
        return $this->render('default/index.html.twig');
    }

    public function buildBreadcrumbs()
    {
        return parent::buildBreadcrumbs()
            ->tailBreadcrumbs('dashboard_homepage');
    }
}