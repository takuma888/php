<?php

namespace Tachigo\Module\UI\Controller\LayoutController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionContainer extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('layout/container.html.twig');
    }
}