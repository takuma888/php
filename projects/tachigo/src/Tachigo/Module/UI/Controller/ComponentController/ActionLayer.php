<?php

namespace Tachigo\Module\UI\Controller\ComponentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionLayer extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('component/layer.html.twig');
    }
}