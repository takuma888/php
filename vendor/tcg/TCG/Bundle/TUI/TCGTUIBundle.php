<?php

namespace TCG\Bundle\TUI;

use TCG\Component\Kernel\Bundle;

class TCGTUIBundle extends Bundle
{

    public function getParent()
    {
        return [
            'TCGTwigBundle',
        ];
    }

}