<?php

namespace Tachigo\Module\UI\Controller\ExtraController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionCard extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('extra/card.html.twig');
    }
}