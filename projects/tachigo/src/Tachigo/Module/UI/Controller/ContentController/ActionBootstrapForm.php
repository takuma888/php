<?php

namespace Tachigo\Module\UI\Controller\ContentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionBootstrapForm extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('content/bootstrap-form.html.twig');
    }
}