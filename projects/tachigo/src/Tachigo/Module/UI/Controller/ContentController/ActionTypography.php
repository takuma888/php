<?php

namespace Tachigo\Module\UI\Controller\ContentController;

use TCG\Bundle\Twig\Component\TwigHttpExec;

class ActionTypography extends TwigHttpExec
{
    public function exec()
    {
        return $this->render('content/typography.html.twig');
    }
}