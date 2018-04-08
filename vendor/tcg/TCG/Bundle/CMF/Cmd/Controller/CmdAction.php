<?php

namespace TCG\Bundle\CMF\Cmd\Controller;

use Symfony\Component\Console\Command\Command;
use TCG\Bundle\CMF\PrivateTrait;

abstract class CmdAction extends Command
{
    use PrivateTrait;
}