<?php

namespace TCG\Bundle\TUI;


trait PublicTrait
{
    /**
     * @return BundleAware
     */
    public function tui()
    {
        return getContainer()->get('tcg_bundle.tui.bundle_aware');
    }
}