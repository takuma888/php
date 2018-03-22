<?php

namespace TCG\Component\Kernel;

abstract class Module extends Bundle
{
    /**
     * @var string
     */
    protected $execNS;

    /**
     * @var string
     */
    protected $execRoot;

    /**
     * Module constructor.
     * @param string $execRoot
     */
    public function __construct($execRoot = '')
    {
        parent::__construct();
        $this->execRoot = trim($execRoot, '/');
        if ($this->execRoot) {
            $execNS = str_replace('/', '\\', $this->execRoot);
            $this->execNS = $this->getNamespace() . '\\' . $execNS;
        } else {
            $this->execNS = $this->getNamespace();
        }
    }

    /**
     * @return string
     */
    public function getExecNamespace()
    {
        return $this->execNS;
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

    /**
     * @return Bundle[]
     */
    public function getBundles()
    {
        return [];
    }

    /**
     * @param $controller
     * @param $action
     * @return Exec
     */
    public function getExec($controller, $action)
    {
        $execClass = $this->getExecNamespace() . '\\Controller\\' . $controller . '\\' . $action;
        $exec = new $execClass($this);
        return $exec;
    }
}