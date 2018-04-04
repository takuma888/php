<?php

namespace TCG\Component\Kernel;

abstract class Bundle
{
    /**
     * @var string
     */
    protected $root;

    /**
     * @var string
     */
    protected $ns;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $execRoot;

    public function __construct($execRoot = '')
    {
        $rc = new \ReflectionClass($this);
        $this->ns = $rc->getNamespaceName();
        $this->root = dirname($rc->getFileName());
        $className = get_class($this);
        $this->name = str_replace($this->getNamespace() . '\\', '', $className);

        $this->execRoot = trim($execRoot, '/');
    }


    public function getNamespace()
    {
        return $this->ns;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getParent()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getExecRoot()
    {
        return $this->execRoot;
    }

    /**
     * @return string[]
     */
    public function getConfigFiles()
    {
        $return = [];
        // globals
        $dir = $this->getRoot() . '/Resource/config';
        if (file_exists($dir) && is_dir($dir)) {
            foreach (glob($dir . '/*') as $configFile) {
                $return[] = realpath($configFile);
            }
        }
        // exec
        if ($this->execRoot) {
            $dir = $this->getRoot() . '/' . $this->execRoot . '/Resource/config';
            if (file_exists($dir) && is_dir($dir)) {
                foreach (glob($dir . '/*') as $configFile) {
                    $return[] = realpath($configFile);
                }
            }
        }
        return $return;
    }

    public function bootKernel(AppKernel $kernel)
    {

    }
}