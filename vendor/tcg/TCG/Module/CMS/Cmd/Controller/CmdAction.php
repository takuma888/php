<?php

namespace TCG\Module\CMS\Cmd\Controller;

use Symfony\Component\Console\Command\Command;
use TCG\Module\CMS\PrivateTrait;

abstract class CmdAction extends Command
{
    use PrivateTrait;
}