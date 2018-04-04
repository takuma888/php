<?php

namespace Tachigo\Module\UI\Controller\ComponentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionTab extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('component/tab.html.twig');
    }
}