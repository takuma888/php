<?php

namespace Tachigo\Module\UI\Controller\LayoutController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionGrid extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('layout/grid.html.twig');
    }
}