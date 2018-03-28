<?php

namespace TCG\Bundle\CMF\Cache\Redis;

use TCG\Component\Cache\Redis\Client as BaseClient;
use TCG\Component\Cache\Redis\ScalarType;

class Client extends BaseClient
{
    /**
     * @return ScalarType
     */
    public function user()
    {
        return getContainer()->get('tcg_bundle.cmf.redis.type.user');
    }

    /**
     * @return ScalarType
     */
    public function role()
    {
        return getContainer()->get('tcg_bundle.cmf.redis.type.role');
    }
}