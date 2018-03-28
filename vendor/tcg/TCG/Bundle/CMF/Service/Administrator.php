<?php

namespace TCG\Bundle\CMF\Service;

use TCG\Bundle\CMF\Database\MySQL\Model\Role;
use TCG\Bundle\CMF\Database\MySQL\Model\User;

class Administrator
{
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

    /**
     * @param $permissionId
     * @return bool
     */
    public function hasPermission($permissionId)
    {
        return isset($this->permissions[$permissionId]);
    }

}