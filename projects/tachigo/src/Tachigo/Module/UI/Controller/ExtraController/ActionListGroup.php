<?php

namespace Tachigo\Module\UI\Controller\ExtraController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionListGroup extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('extra/list-group.html.twig');
    }
}