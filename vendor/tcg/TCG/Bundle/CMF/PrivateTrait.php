<?php

namespace TCG\Bundle\CMF;


use TCG\Bundle\CMF\Component\Directory;
use TCG\Bundle\CMF\Database\MySQL\LogClient as LogDbClient;
use TCG\Bundle\CMF\Database\MySQL\MainClient as MainDbClient;
use TCG\Bundle\CMF\DataProvider\LogProvider;
use TCG\Bundle\CMF\DataProvider\RoleProvider;
use TCG\Bundle\CMF\DataProvider\UserProvider;
use TCG\Bundle\CMF\Cache\Redis\Client as CacheClient;
use TCG\Bundle\CMF\Service\PassportService;
use TCG\Bundle\CMF\Service\RoleService;
use TCG\Bundle\CMF\Service\UserService;

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
     * @return UserService
     */
    public function serviceUser()
    {
        return getContainer()->get('tcg_bundle.cmf.service.user');
    }

    /**
     * @return RoleService
     */
    public function serviceRole()
    {
        return getContainer()->get('tcg_bundle.cmf.service.role');
    }

    /**
     * @return PassportService
     */
    public function servicePassport()
    {
        return getContainer()->get('tcg_bundle.cmf.service.passport');
    }

    /**
     * @return CacheClient
     */
    public function cache()
    {
        return getContainer()->get('tcg_bundle.cmf.redis.client');
    }

    /**
     * @return Directory
     */
    public function toolDirectory()
    {
        return getContainer()->get('tcg_bundle.cmf.component.directory');
    }
}