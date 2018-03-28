<?php


namespace TCG\Component\Cache;


interface CacheInterface
{

    public function set($key, $value, $expire = 0);

    public function get($key);

    public function delete($key);
}