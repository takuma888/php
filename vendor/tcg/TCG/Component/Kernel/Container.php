<?php

namespace TCG\Component\Kernel;

use TCG\Component\Util\StringUtil;

abstract class Container
{

    protected $services = [];

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getService($name)
    {
        $getter = 'get' . StringUtil::camelcase($name) . 'Service';
        if (!method_exists($this, $getter)) {
            throw new \Exception("service $name not found!");
        }
        return $this->$getter();
    }

    public function get($id)
    {
        return $this->getService($id);
    }

    public function has($id)
    {
        return $this->hasService($id);
    }

    public function hasService($id)
    {
        $getter = 'get' . StringUtil::camelcase($id) . 'Service';
        return method_exists($this, $getter) ? true : false;
    }


    public function tag($name)
    {
        $return = $this->getTagService($name);
        return $return;
    }


    public function hasTagService($name)
    {
        $getter = 'get' . StringUtil::camelcase($name) . 'Tag';
        return method_exists($this, $getter) ? true : false;
    }

    public function getTagService($name)
    {
        $getter = 'get' . StringUtil::camelcase($name) . 'Tag';
        if (!method_exists($this, $getter)) {
            throw new \Exception("Tag service $name not found!");
        }
        return $this->$getter();
    }

    /**
     * @param $name
     * @return mixed|Config|string|number|bool
     */
    public function getParameter($name)
    {
        $configuration = $this->getKernel()->getConfig();
        $parameters = $configuration['parameters'];
        return $parameters[$name];
    }

    /**
     * @return Config
     */
    public function getParameters()
    {
        $configuration = $this->getKernel()->getConfig();
        return $configuration['parameters'];
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $configuration = $this->getKernel()->getConfig();
        $configuration['parameters'] = new Config($parameters);
    }

    /**
     * @return Kernel
     */
    abstract public function getKernel();
}