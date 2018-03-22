<?php

namespace TCG\Component\Kernel;

abstract class Exec
{

    /**
     * @var Module
     */
    private $module;

    /**
     * Exec constructor.
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    public function __invoke()
    {
        return $this->exec();
    }

    abstract public function exec();

}