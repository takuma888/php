<?php

namespace TCG\Module\CMS;

use TCG\Bundle\TUI\PublicTrait as TUITrait;
use TCG\Bundle\CMF\PublicTrait as CMFTrait;
use TCG\Bundle\CMF\CMFExec;

abstract class CMSExec extends CMFExec
{
    use CMFTrait;
    use TUITrait;
    use PrivateTrait;
}