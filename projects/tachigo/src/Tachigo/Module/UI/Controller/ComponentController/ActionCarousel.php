<?php

namespace Tachigo\Module\UI\Controller\ComponentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionCarousel extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('component/carousel.html.twig');
    }
}