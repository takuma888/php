<?php

namespace TCG\Component\Kernel;

abstract class Module extends Bundle
{
    /**
     * @var string
     */
    protected $execNS;

    /**
     * Module constructor.
     * @param string $execRoot
     */
    public function __construct($execRoot = '')
    {
        parent::__construct($execRoot);
        if ($this->execRoot) {
            $execNS = str_replace('/', '\\', $this->execRoot);
            $this->execNS = $this->getNamespace() . '\\' . $execNS;
        } else {
            $this->execNS = $this->getNamespace();
        }
    }

    /**
     * @return Bundle[]
     */
    public function getBundles()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getExecNamespace()
    {
        return $this->execNS;
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