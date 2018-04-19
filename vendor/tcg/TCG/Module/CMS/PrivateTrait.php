<?php

namespace TCG\Module\CMS;

use TCG\Module\CMS\Database\MySQL\MainClient as MainDbClient;
use TCG\Module\CMS\Service\SessionService;

trait PrivateTrait
{
    /**
     * @return MainDbClient
     */
    public function dbMain()
    {
        return getContainer()->get('tcg_module.cms.mysql.main.client');
    }

    /**
     * @return SessionService
     */
    public function serviceSession()
    {
        return getContainer()->get('tcg_module.cms.service.session');
    }

}