<?php

namespace Tachigo\Module\UI\Controller\VendorController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionLayer extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('vendor/layer.html.twig');
    }
}