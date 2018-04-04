<?php

namespace TCG\Module\CMS;


trait PublicTrait
{
    /**
     * @return ModuleAware
     */
    public function tcgCMS()
    {
        return getContainer()->get('tcg_module.cms.module_aware');
    }
}