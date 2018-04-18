<?php

namespace TCG\Bundle\CMF\Cmd\Controller\AccountController;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TCG\Bundle\CMF\Cmd\Controller\CmdAction;

class ActionCreateRoles extends CmdAction
{
    protected function configure()
    {
        $this
            ->setName('tcg_cmf:account.create_roles')
            ->setDescription('创建CMF的默认角色');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootRole = $this->serviceRole()
            ->createDefaultRoot();
        $rootRole->name = '根角色';
        $rootRole->description = '所有的角色都在这个根角色下';
        $rootRole->update();
        // 超级管理员
        $superAdminRole = $this->serviceRole()
            ->createDefaultSuperAdmin($rootRole);
        $superAdminRole->name = '超级管理员';
        $superAdminRole->description = '拥有所有权限的角色，用用所有权限';
        $superAdminRole->update();
        // 开发人员
        $developerRole = $this->serviceRole()
            ->createDefaultDeveloper($rootRole);
        $developerRole->name = '开发者';
        $developerRole->description = '由于开发原因，拥有所有权限';
        $developerRole->update();
        // 测试角色
        $testRole = $this->serviceRole()
            ->createDefaultTest($rootRole);
        $testRole->name = '测试角色';
        $testRole->description = '这个角色是用来测试的，随便设置什么权限';
        $testRole->update();
    }
}