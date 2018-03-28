<?php

namespace TCG\Bundle\CMF;


use TCG\Bundle\CMF\Database\MySQL\LogClient as LogDbClient;
use TCG\Bundle\CMF\Database\MySQL\MainClient as MainDbClient;
use TCG\Bundle\CMF\DataProvider\LogProvider;
use TCG\Bundle\CMF\DataProvider\RoleProvider;
use TCG\Bundle\CMF\DataProvider\UserProvider;
use TCG\Bundle\CMF\Cache\Redis\Client as CacheClient;

trait PrivateTrait
{
    /**
     * @return MainDbClient
     */
    public function dbMain()
    {
        return getContainer()->get('tcg_bundle.cmf.mysql.main.client');
    }

    /**
     * @return LogDbClient
     */
    public function dbLog()
    {
        return getContainer()->get('tcg_bundle.cmf.mysql.log.client');
    }

    /**
     * @return UserProvider
     */
    public function providerUser()
    {
        return getContainer()->get('tcg_bundle.cmf.data_provider.user');
    }

    /**
     * @return RoleProvider
     */
    public function providerRole()
    {
        return getContainer()->get('tcg_bundle.cmf.data_provider.role');
    }


    /**
     * @return LogProvider
     */
    public function providerLog()
    {
        return getContainer()->get('tcg_bundle.cmf.data_provider.log');
    }

    /**
     * @return CacheClient
     */
    public function cache()
    {
        return getContainer()->get('tcg_bundle.cmf.redis.client');
    }
}