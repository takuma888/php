<?php

namespace TCG\Module\CMS;

use TCG\Module\CMS\Database\MySQL\MainClient as MainDbClient;

trait PrivateTrait
{
    /**
     * @return MainDbClient
     */
    public function dbMain()
    {
        return getContainer()->get('tcg_module.cms.mysql.main.client');
    }
}