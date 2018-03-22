<?php

namespace TCG\Module\Web\Controller\DefaultController;

use TCG\Bundle\Http\Component\HttpExec;

class ActionIndex extends HttpExec
{
    public function exec()
    {
        return $this->jump('http://baidu.com');
    }
}