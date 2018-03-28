<?php

namespace TCG\Bundle\CMF;

trait PublicTrait
{
    /**
     * @return BundleAware
     */
    public function tcgCMF()
    {
        return getContainer()->get('tcg_bundle.cmf.bundle_aware');
    }
}