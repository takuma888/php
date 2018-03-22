<?php

namespace Tachigo\Module\UI\Controller\ExtraController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionBadge extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('extra/badge.html.twig');
    }
}