<?php

namespace TCG\Bundle\CMF;

use TCG\Component\Kernel\Bundle;

class TCGCMFBundle extends Bundle
{
    public function getParent()
    {
        return [
            'TCGTwigBundle'
        ];
    }
}