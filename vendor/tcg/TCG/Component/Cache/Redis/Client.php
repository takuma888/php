<?php

namespace TCG\Component\Cache\Redis;

use Psr\Log\LoggerInterface;

class Client
{

    /**
     * @var string
     */
    protected $host;

    /**
     * @var number
     */
    protected $port;

    /**
     * @var string
     */
    protected $pass;

    /**
     * @var number
     */
    protected $db;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var Connection[]
     */
    protected static $redis = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;


    public function __construct(array $config)
    {
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->pass = $config['pass'];
        $this->db = $config['db'];
        $this->prefix = $config['prefix'];
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }


    /**
     * @return Connection
     */
    public function connection()
    {
        $key = $this->host . ':' . $this->port;
        if (isset(self::$redis[$key])) {
            try {
                self::$redis[$key]->ping();
            } catch (\Exception $e) {
                unset(self::$redis[$key]);
            }
        }
        if (!isset(self::$redis[$key])) {
            $redis = new Connection($this);
            try {
                $redis->connect($this->host, $this->port);
            } catch (\Exception $e) {
                throw new $e;
            }
            if ($this->pass) {
                $redis->auth($this->pass);
            }
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            if ($this->db !== null) {
                $redis->select($this->db);
            }
            self::$redis[$key] = $redis;
        }
        return self::$redis[$key];
    }

}