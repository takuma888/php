<?php

namespace TCG\Component\Cache\Redis;

abstract class Type
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $name;

    /**
     * HashMap constructor.
     * @param Client $client
     * @param null $name
     */
    public function __construct(Client $client, $name = null)
    {
        $this->client;
        $this->name = $name;
    }


    public function setName($name)
    {
        $this->name = $name;
    }


    public function getName()
    {
        return $this->name;
    }


    public function getKey($key = null)
    {
        return trim($this->client->getPrefix(), ':') . ':' . $this->name . ($key ? ':' . $key : '');
    }
}