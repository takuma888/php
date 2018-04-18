<?php

namespace TCG\Bundle\CMF\Service;

use TCG\Bundle\CMF\Database\MySQL\Model\Role;
use TCG\Bundle\CMF\Database\MySQL\Model\User;

class Account
{

    const ROLE_ROOT = 'root';
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_DEVELOPER = 'developer';
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Role[]
     */
    protected $roles = [];

    /**
     * @var string[]
     */
    protected $permissions = [];

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles[$role->key] = $role;
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param $roleKey
     * @return bool
     */
    public function hasRole($roleKey)
    {
        return isset($this->roles[$roleKey]);
    }

    /**
     * @param $permissionId
     */
    public function addPermission($permissionId)
    {
        $this->permissions[$permissionId] = $permissionId;
    }


    public function isRoot()
    {
        return $this->hasRole(self::ROLE_ROOT);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isDeveloper()
    {
        return $this->hasRole(self::ROLE_DEVELOPER);
    }

    /**
     * @param $permissionId
     * @return bool
     */
    public function hasPermission($permissionId)
    {
        $rolesWhitelist = [
            self::ROLE_ROOT, self::ROLE_SUPER_ADMIN, self::ROLE_DEVELOPER,
        ];
        foreach ($rolesWhitelist as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return isset($this->permissions[$permissionId]);
    }

}