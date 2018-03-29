<?php

namespace TCG\Bundle\CMF\Service;


use TCG\Bundle\CMF\CMFException;
use TCG\Bundle\CMF\Database\MySQL\Model\Role;
use TCG\Bundle\CMF\Database\MySQL\Model\User;
use TCG\Bundle\CMF\PrivateTrait;
use TCG\Component\Database\MySQL\Query\QueryBuilder;

class RoleService
{
    use PrivateTrait;

    /**
     * @param $key
     * @param Role|null $parentRole
     * @return string|Role
     * @throws CMFException
     */
    public function create($key, Role $parentRole = null)
    {
        $key = trim($key);
        if (!$key) {
            throw new CMFException("key不能为空", CMFException::CODE_CREATE_ROLE_BUT_KEY_EMPTY);
        }
        // 检查重复性
        if (!$parentRole) {
            // 创建根
            // 检查根是否存在
            $root = $this->dbMain()
                ->tblRoles()
                ->getRootNode();
            if ($root) {
                throw new CMFException("根角色已存在", CMFException::CODE_CREATE_ROLE_BUT_ROOT_EXISTS);
            }
            $rootRole = $this->dbMain()
                ->tblRoles()
                ->model();
            $rootRole->key = $key;
            $rootRole->leftValue = 0;
            $rootRole->rightValue = 1;
            return $this->providerRole()
                ->insert($rootRole);
        } else {
            $tmp = $this->providerRole()
                ->oneBy('key', $key);
            if ($tmp) {
                throw new CMFException("角色已存在", CMFException::CODE_CREATE_ROLE_BUT_KEY_EXISTS);
            }
            $role = $parentRole->insertChild([
                'key' => $key,
            ], true);
            return $role;
        }
    }


    public function remove(Role $role)
    {
        $this->providerRole()
            ->remove($role);
    }


    public function updateUserRole(User $user, array $roleIds)
    {
        // 清除原有的用户角色关联关系
        $this->dbMain()
            ->tblUser2Role()
            ->delete(function (QueryBuilder $queryBuilder) use ($user) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('user_id', ':user_id'))->setParameter(':user_id', $user->id);
            });
        if ($roleIds) {
            // 批量插入新的关联关系
            $user2role = [];
            foreach ($roleIds as $roleId) {
                $user2role[] = [
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ];
            }
            $this->dbMain()
                ->tblUser2Role()
                ->multiInsert($user2role);
        }
    }



    public function updateRolePermissions(Role $role, array $permissionIds)
    {
        // 先清空原有的角色权限关联关系
        $this->dbMain()
            ->tblRole2Permission()
            ->delete(function (QueryBuilder $queryBuilder) use ($role) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('role_id', ':role_id'))->setParameter(':role_id', $role->id);
            });
        if ($permissionIds) {
            // 批量插入新的关联关系
            $role2permission = [];
            foreach ($permissionIds as $permissionId) {
                $role2permission[] = [
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                ];
            }
            $this->dbMain()
                ->tblRole2Permission()
                ->multiInsert($role2permission);
        }
    }


    /**
     * @param Role $role
     * @return RoleTree
     */
    public function roleTree(Role $role)
    {
        $subTree = $role->getSubTree();
        return new RoleTree($subTree);
    }
}