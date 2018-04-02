<?php

namespace Tachigo\Module\UI\Controller\ComponentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionCollapse extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('component/collapse.html.twig');
    }
}