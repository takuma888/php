<?php

namespace Tachigo\Module\UI\Controller\DefaultController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionIndex extends TwigHttpExec
{

    public function exec()
    {
        return $this->render('default/index.html.twig');
    }
}