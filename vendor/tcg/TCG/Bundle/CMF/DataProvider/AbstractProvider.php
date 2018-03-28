<?php

namespace TCG\Bundle\CMF\DataProvider;

use TCG\Bundle\CMF\PrivateTrait;
use TCG\Component\Database\MySQL\Client as RedisClient;
use TCG\Component\Database\MySQL\Client as MySQLClient;

class AbstractProvider
{
    use PrivateTrait;
    /**
     * @var MySQLClient
     */
    protected $mysql;

    /**
     * @var RedisClient
     */
    protected $redis;

    /**
     * @param MySQLClient $client
     */
    public function setMySQL(MySQLClient $client)
    {
        $this->mysql = $client;
    }

    /**
     * @return MySQLClient
     */
    public function getMySQL()
    {
        return $this->mysql;
    }

    /**
     * @param MySQLClient $client
     */
    public function setRedis(RedisClient $client)
    {
        $this->redis = $client;
    }

    /**
     * @return MySQLClient
     */
    public function getRedis()
    {
        return $this->redis;
    }
}