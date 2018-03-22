<?php

namespace Tachigo\Module\UI\Controller\ComponentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionAlert extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('component/alert.html.twig');
    }
}