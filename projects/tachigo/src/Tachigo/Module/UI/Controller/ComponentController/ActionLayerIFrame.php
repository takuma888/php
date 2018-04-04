<?php

namespace Tachigo\Module\UI\Controller\ComponentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionLayerIFrame extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('component/layer-iframe.html.twig');
    }
}