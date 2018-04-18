<?php

namespace TCG\Bundle\CMF\Cmd\Controller\AccountController;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TCG\Bundle\CMF\Cmd\Controller\CmdAction;

class ActionCreateUser extends CmdAction
{

    protected function configure()
    {
        $this
            ->setName('tcg_cmf:account.create_user')
            ->setDescription('创建CMF的用户')
            ->addOption('username', null, InputOption::VALUE_OPTIONAL, '用户名', '')
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, '密码', '')
            ->addOption('roles', null, InputOption::VALUE_OPTIONAL, '角色', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = trim($input->getOption('username'));
        $password = trim($input->getOption('password'));
        $roles = trim($input->getOption('roles'));

        if (!$username) {
            throw new \Exception("用户名不能为空");
        }
        if (!$password) {
            throw new \Exception("密码不能为空");
        }
        if (!$roles) {
            throw new \Exception("角色不能为空");
        }
        $roles = explode(',', $roles);

        $transaction = function () use ($username, $password, $roles) {
            // 创建用户
            $user = $this->serviceUser()
                ->create($username, null, null, $password);
            $roleIds = [];
            // 创建角色
            foreach ($roles as $roleKey) {
                $role = $this->providerRole()->oneBy('key', $roleKey);
                if ($role) {
                    $roleIds[] = $role->id;
                } else {
                    // 创建新角色
                    $rootRole = $this->serviceRole()->getRoot();
                    if ($rootRole->key != $roleKey) {
                        $roleIds[] = $rootRole->insertChild([
                            'key' => $roleKey,
                        ]);
                    } else {
                        $roleIds[] = $rootRole->id;
                    }
                }
            }
            $this->serviceRole()
                ->updateUserAdminRole($user, $roleIds);
        };
        $transaction->bindTo($this);

        $this->dbMain()->transaction($transaction);
    }
}