<?php

namespace Tachigo\Module\UI\Controller\ComponentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionPopout extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('component/popout.html.twig');
    }
}