<?php

namespace Tachigo\Module\UI\Controller\ExtraController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionBreadcrumb extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('extra/breadcrumb.html.twig');
    }
}