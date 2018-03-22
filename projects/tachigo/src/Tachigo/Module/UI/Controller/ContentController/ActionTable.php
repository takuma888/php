<?php

namespace Tachigo\Module\UI\Controller\ContentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionTable extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('content/table.html.twig');
    }
}