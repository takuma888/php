<?php

namespace TCG\Component\Cache\Redis;


use TCG\Component\Cache\CacheInterface;

class ScalarType extends Type implements CacheInterface
{

    public function set($key, $value, $expire = 0)
    {
    }


    public function get($key)
    {
    }
}