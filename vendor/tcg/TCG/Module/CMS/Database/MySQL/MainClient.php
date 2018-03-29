<?php

namespace TCG\Module\CMS\Database\MySQL;

use TCG\Component\Database\MySQL\Client;
use TCG\Module\CMS\Database\MySQL\Table\Sessions;

class MainClient extends Client
{
    /**
     * @return Sessions
     */
    public function tblSessions()
    {
        return getContainer()->get('tcg_module.cms.mysql.main.table.sessions');
    }
}