<?php

namespace TCG\Bundle\TUI;

use TCG\Bundle\TUI\Service\DataTableService;

trait PrivateTrait
{
    /**
     * @return DataTableService
     */
    public function serviceDataTable()
    {
        return getContainer()->get('tcg_bundle.tui.service.data_table');
    }
}