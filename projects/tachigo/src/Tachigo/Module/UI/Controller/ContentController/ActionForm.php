<?php

namespace Tachigo\Module\UI\Controller\ContentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionForm extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('content/form.html.twig');
    }
}