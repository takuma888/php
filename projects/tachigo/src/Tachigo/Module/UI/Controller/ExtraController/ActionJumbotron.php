<?php

namespace Tachigo\Module\UI\Controller\ExtraController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionJumbotron extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('extra/jumbotron.html.twig');
    }
}