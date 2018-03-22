<?php


namespace Tachigo\Module\UI\Controller\TestController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionNormalize extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('test/normalize.html.twig');
    }
}