<?php

namespace
{

    use TCG\Component\Kernel\AppKernel;

    /**
     * @param $tmpKernelNS
     * @return \TCG\Component\Kernel\Container
     * @throws \Exception
     */
    function getContainer($tmpKernelNS = '')
    {
        $oldKernelNS = '';
        if ($tmpKernelNS) {
            $oldKernelNS = AppKernel::setCurrentKernelNamespace($tmpKernelNS);
        }
        $kernelNS = AppKernel::getCurrentKernelNamespace();
        $container = AppKernel::getInstance($kernelNS)->getContainer();
        AppKernel::setCurrentKernelNamespace($oldKernelNS);
        return $container;
    }
}

namespace TCG\Component\Kernel
{
    use Composer\Autoload\ClassLoader;

    class AppKernel extends Kernel
    {
        /**
         * @var AppKernel[]
         */
        protected static $instances = [];

        /**
         * @var string[]
         */
        protected static $kernelStack = [];

        /**
         * @var string
         */
        protected static $currentKernelNamespace = '';


        public function __construct(ClassLoader $autoLoader, array $config = [], $ns = '')
        {
            $ns = str_replace(['.', '/'], ['_'], $ns);
            if (isset(self::$instances[$ns])) {
                throw new \Exception('核心实例已经实例化过了，可以通过' . __NAMESPACE__ . '\AppKernel::getInstance(' . $ns . ')获得');
            }
            self::$kernelStack[] = $ns;
            self::$instances[$ns] = $this;
            parent::__construct($autoLoader, $config, $ns);
        }

        /**
         * @return string
         * @throws \Exception
         */
        public static function getCurrentKernelNamespace()
        {
            if (empty(self::$currentKernelNamespace)) {
                if (empty(self::$kernelStack)) {
                    throw new \Exception('当前 AppKernel 调用栈为空!');
                }
                self::$currentKernelNamespace = array_pop(self::$kernelStack);
            }
            return self::$currentKernelNamespace;
        }

        /**
         * @param $kernelNS
         * @return string
         */
        public static function setCurrentKernelNamespace($kernelNS)
        {
            $ns = str_replace(['.', '/'], ['_'], $kernelNS);
            $oldKernelNS = '';
            if (!empty(self::$currentKernelNamespace)) {
                self::$kernelStack[] = self::$currentKernelNamespace;
                $oldKernelNS = self::$currentKernelNamespace;
            }
            self::$currentKernelNamespace = $ns;
            return $oldKernelNS;
        }


        /**
         * @param $ns
         * @return AppKernel
         * @throws
         */
        public static function getInstance($ns)
        {
            $ns = str_replace(['.', '/'], ['_'], $ns);
            if (!isset(self::$instances[$ns])) {
                throw new \Exception('需要通过 new AppKernel() 来创建实例！');
            }
            return self::$instances[$ns];
        }

        /**
         * @var bool
         */
        protected $booted = false;


        /**
         * @return $this
         */
        public function boot()
        {
            if (!$this->booted) {
                foreach ($this->bundles as $bundle) {
                    $bundle->bootKernel($this);
                }
                foreach ($this->modules as $module) {
                    $module->bootKernel($this);
                }
                $container = $this->getContainer();
                $container->getKernel()->getConfig()->setReadOnly(true);
                $this->booted = true;
            }
            return $this;
        }

        /**
         * @var StartKernelInterface
         */
        protected $entry;

        /**
         * @param StartKernelInterface $entry
         */
        public function setEntry(StartKernelInterface $entry)
        {
            $this->entry = $entry;
        }

        /**
         * @return StartKernelInterface
         */
        public function getEntry()
        {
            return $this->entry;
        }

        /**
         * 启动核心
         */
        public function startup()
        {
            $entry = $this->getEntry();
            if ($entry) {
                $entry->run($this);
            }
        }
    }
}

