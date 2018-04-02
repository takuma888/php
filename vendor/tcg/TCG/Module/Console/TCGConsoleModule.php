<?php

namespace TCG\Module\Console;

use TCG\Component\Kernel\AppKernel;
use TCG\Component\Kernel\Module;
use TCG\Component\Kernel\StartKernelInterface;
use TCG\Module\Console\Component\Application;

class TCGConsoleModule extends Module implements StartKernelInterface
{
    public function bootKernel(AppKernel $kernel)
    {
        $kernel->setEntry($this);
    }


    public function run(AppKernel $kernel)
    {
        /** @var Application $consoleApp */
        $consoleApp = getContainer()->get('tcg_module.console.application');
        $consoleApp->run();
    }
}