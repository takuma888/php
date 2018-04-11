<?php

namespace TCG\Bundle\CMF\Cmd\Controller\AccountController;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TCG\Bundle\CMF\Cmd\Controller\CmdAction;

class ActionCreateRootRole extends CmdAction
{
    protected function configure()
    {
        $this
            ->setName('tcg_cmf:account.create_root_role')
            ->setDescription('创建CMF的根角色');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->serviceRole()
            ->createDefaultRoot();
    }
}