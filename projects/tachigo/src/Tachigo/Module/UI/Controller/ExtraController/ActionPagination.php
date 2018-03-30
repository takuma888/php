<?php

namespace Tachigo\Module\UI\Controller\ExtraController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionPagination extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('extra/pagination.html.twig');
    }
}