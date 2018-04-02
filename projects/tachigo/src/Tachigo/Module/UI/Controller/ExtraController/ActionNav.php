<?php

namespace Tachigo\Module\UI\Controller\ExtraController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionNav extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('extra/nav.html.twig');
    }
}