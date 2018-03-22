<?php

namespace Tachigo\Module\UI\Controller\TestController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionUINormalize extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('test/ui-normalize.html.twig');
    }
}