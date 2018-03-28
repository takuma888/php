<?php

namespace TCG\Component\Cache\Redis;


use TCG\Component\Cache\CacheInterface;

class ScalarType extends Type implements CacheInterface
{
    /**
     * @param $key
     * @param $value
     * @param int $expire
     */
    public function set($key, $value, $expire = 0)
    {
        $key = $this->getKey($key);
        $this->client->connection()
            ->set($key, $value, $expire);
    }

    /**
     * @param $key
     * @return bool|mixed|string
     */
    public function get($key)
    {
        $key = $this->getKey($key);
        return $this->client->connection()
            ->get($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        $key = $this->getKey($key);
        return $this->client->connection()
            ->delete($key);
    }
}