<?php

namespace TCG\Bundle\CMF\Service;

use TCG\Bundle\CMF\CMFException;
use TCG\Bundle\CMF\Database\MySQL\Model\User;
use TCG\Bundle\CMF\PrivateTrait;

class UserService
{
    use PrivateTrait;

    /**
     * @param $username
     * @param $email
     * @param $mobile
     * @param $password
     * @return \TCG\Bundle\CMF\Database\MySQL\Model\User
     * @throws CMFException
     */
    public function create($username, $email, $mobile, $password)
    {
        $username = trim($username);
        $email = trim($email);
        $mobile = trim($mobile);

        // 验证用户名，邮箱，手机号码是否为空
        if (!$username && !$email && !$mobile) {
            throw new CMFException("用户名，邮箱，手机号码不能都为空", CMFException::CODE_CREATE_USER_BUT_USERNAME_EMAIL_MOBILE_ALL_EMPTY);
        }
        // 检查重复性
        $tmp = $this->providerUser()
            ->oneBy('username', $username);
        if ($tmp) {
            throw new CMFException("用户名已存在", CMFException::CODE_CREATE_USER_BUT_USERNAME_EXISTS);
        }
        $tmp = $this->providerUser()
            ->oneBy('email', $email);
        if ($tmp) {
            throw new CMFException("邮箱已存在", CMFException::CODE_CREATE_USER_BUT_EMAIL_EXISTS);
        }
        $tmp = $this->providerUser()
            ->oneBy('mobile', $mobile);
        if ($tmp) {
            throw new CMFException("手机号码已存在", CMFException::CODE_CREATE_USER_BUT_MOBILE_EXISTS);
        }

        // 验证密码
        $password = trim($password);
        if (!$password) {
            throw new CMFException("密码不能为空", CMFException::CODE_CREATE_USER_BUT_PASSWORD_EMPTY);
        }

        // 创建
        $user = $this->dbMain()->tblUsers()->model();
        $user->username = $username ? $username : null;
        $user->email = $email ? $email : null;
        $user->mobile = $mobile ? $mobile : null;
        $user->password = md5($password);

        $this->providerUser()
            ->insert($user);
        return $user;
    }

    /**
     * @param User $user
     * @param $password
     * @return bool
     */
    public function checkPassword(User $user, $password)
    {
        return $user->password == md5($password);
    }


    public function remove(User $user)
    {
        $this->providerUser()
            ->remove($user);
    }

    /**
     * @param User $user
     * @return Account
     */
    public function account(User $user)
    {
        $account = new Account();
        $account->setUser($user);

        // 查询出角色
        $roles = $this->providerRole()
            ->allByUser($user);
        foreach ($roles as $role) {
            $account->addRole($role);
            // 权限
            $permissions = $this->providerRole()
                ->permissionsByRole($role);
            foreach ($permissions as $permissionId) {
                $account->addPermission($permissionId);
            }
        }

        return $account;
    }
}