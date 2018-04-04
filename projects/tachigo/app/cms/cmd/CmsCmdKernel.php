<?php

namespace
{

    use TCG\Module\Base\TCGBaseModule;

    use Composer\Autoload\ClassLoader;
    use Symfony\Component\Debug\Debug;
    use TCG\Component\Kernel\AppKernel;
    use TCG\Module\CMS\TCGCMSModule;
    use TCG\Module\Console\TCGConsoleModule;

    Debug::enable();

    class CmsCmdKernel extends AppKernel
    {

        public function __construct(ClassLoader $autoLoader)
        {
            parent::__construct($autoLoader, [
                'root' => ROOT,
                'app_root' => APP_ROOT,
                'log_root' => LOG_ROOT,
                'src_root' => SRC_ROOT,
                'cache_root' => CACHE_ROOT,
                'vendor_root' => VENDOR_ROOT,
            ], __CLASS__);
            parent::setCurrentKernelNamespace($this->getNamespace());


            $this->addModules([
                new TCGBaseModule(),
                new TCGConsoleModule(),
                new TCGCMSModule(TCGCMSModule::EXEC_CMD),
            ]);
        }
    }
}