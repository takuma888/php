<?php

namespace TCG\Component\Util;


trait ArrayTrait
{

    protected $data = array();

    // Implement \IteratorAggregate

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    // Implement \Iterator

    /*public function current()
    {
        return current($this->data);
    }


    public function next()
    {
        return next($this->data);
    }


    public function rewind()
    {
        reset($this->data);
    }


    public function key()
    {
        return key($this->data);
    }


    public function valid()
    {
        return current($this->data) != null;
    }*/



    // Implement \Countable


    public function count()
    {
        return count($this->data);
    }

    // Implement \ArrayAccess

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }


    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }


    public function offsetExists($offset)
    {
        try {
            return $this->get($offset) != null;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    public function __get($key)
    {
        $key = $this->underscore($key);
        return $this->get($key);
    }


    public function __set($key, $value)
    {
        $key = $this->underscore($key);
        $this->set($key, $value);
    }


    public function add($data)
    {
        $this->data[] = $data;
    }


    public function push($data)
    {
        $this->add($data);
    }

    public function clear()
    {
        $this->data = array();
    }


    abstract public function get($key, $default = null);

    abstract public function set($key, $value);



    public function isEmpty()
    {
        return $this->count() == 0;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    protected function camelize($string)
    {
        return strtr(ucwords(strtolower(strtr($string, array('_' => ' ',)))), array(' ' => ''));
    }


    protected function underscore($string)
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($string, '_', '.')));
    }
}