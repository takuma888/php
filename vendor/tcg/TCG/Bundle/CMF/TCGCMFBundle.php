<?php

namespace TCG\Bundle\CMF;

use TCG\Component\Kernel\Bundle;

class TCGCMFBundle extends Bundle
{

    const EXEC_CMD = 'Cmd';


    public function getParent()
    {
        if ($this->execRoot != self::EXEC_CMD) {
            return [
                'TCGTwigBundle'
            ];
        } else {
            return [];
        }
    }
}