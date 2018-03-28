<?php

namespace TCG\Bundle\CMF\Database\MySQL;

use TCG\Bundle\CMF\Database\MySQL\Table\Logs;
use TCG\Component\Database\MySQL\Client;

class LogClient extends Client
{

    /**
     * @return Logs
     */
    public function tblLogs()
    {
        return getContainer()->get('tcg_bundle.cmf.mysql.log.table.logs');
    }
}