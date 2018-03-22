<?php

namespace Tachigo\Bundle\UI;

use TCG\Component\Kernel\Bundle;

class TachigoUIBundle extends Bundle
{

    public function getParent()
    {
        return [
            'TCGTwigBundle',
        ];
    }

}