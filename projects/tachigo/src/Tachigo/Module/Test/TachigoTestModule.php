<?php

namespace Tachigo\Module\Test;

use TCG\Component\Kernel\AppKernel;
use TCG\Component\Kernel\Module;
use TCG\Component\Kernel\StartKernelInterface;

class TachigoTestModule extends Module implements StartKernelInterface
{



    public function bootKernel(AppKernel $kernel)
    {
        $kernel->setEntry($this);
    }


    public function run(AppKernel $kernel)
    {
        echo 'aaaaaa';
    }
}