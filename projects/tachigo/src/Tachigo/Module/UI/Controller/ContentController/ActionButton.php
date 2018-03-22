<?php

namespace Tachigo\Module\UI\Controller\ContentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionButton extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('content/button.html.twig');
    }
}