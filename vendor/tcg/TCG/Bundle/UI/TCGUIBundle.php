<?php

namespace TCG\Bundle\UI;

use TCG\Component\Kernel\Bundle;

class TCGUIBundle extends Bundle
{

    public function getParent()
    {
        return [
            'TCGTwigBundle',
        ];
    }

}