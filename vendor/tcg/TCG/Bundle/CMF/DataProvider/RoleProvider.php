<?php

namespace TCG\Bundle\CMF\DataProvider;


use TCG\Bundle\CMF\Database\MySQL\Model\Role;
use TCG\Bundle\CMF\Database\MySQL\Model\User;
use TCG\Component\Database\MySQL\Query\QueryBuilder;

class RoleProvider extends AbstractProvider
{
    /**
     * @param $key
     * @param $value
     * @return bool|mixed|null|string|Role|\TCG\Component\Database\MySQL\Model
     */
    public function oneBy($key, $value)
    {
        $data = $this->oneByFromCache($key, $value);
        if (!$data) {
            $data = $this->oneByFromDb($key, $value);
            if ($data) {
                $this->oneByToCache($key, $value, $data);
                $this->cacheUpdate($data, [$key]);
                $data = $this->dbMain()
                    ->tblRoles()
                    ->model($data);
            }
        }
        return $data;
    }

    public function oneById($id)
    {
        return $this->oneBy('id', $id);
    }


    public function oneByFromDb($key, $value)
    {
        return $this->dbMain()
            ->tblRoles()
            ->one(function (QueryBuilder $queryBuilder) use ($key, $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq($key, ":{$key}"))->setParameter(":{$key}", $value);
            });
    }


    public function oneByFromCache($key, $value)
    {
        return $this->cache()
            ->user()
            ->get("{$key}:{$value}");
    }


    public function oneByToCache($key, $value, $data)
    {
        $this->cache()
            ->user()
            ->set("{$key}:{$value}", $data);
    }


    public function oneByClearCache($key, $value)
    {
        return $this->cache()
            ->user()
            ->delete("{$key}:{$value}");
    }


    public function cacheUpdate($data, array $excludes = [])
    {
        if (!in_array('id', $excludes)) {
            $this->oneByToCache('id', $data['id'], $data);
        }
    }


    public function cacheClear($data)
    {
        $this->oneByClearCache('id', $data['id']);
    }


    /**
     * @param Role $role
     * @return Role
     */
    public function insert(Role $role)
    {
        $now = time();
        $role['create_at'] = $now;
        $role->insert();
        $data = $role->toRawArray();
        $this->cacheUpdate($data);
        return $role;
    }


    public function remove(Role $role)
    {
        // 删除角色
        $role->remove();
        $data = $role->toRawArray();
        $this->cacheClear($data);
        // 删除和角色关联的用户关系
        $this->dbMain()
            ->tblUser2Role()
            ->delete(function (QueryBuilder $queryBuilder) use ($data) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('role_id', ':role_id'))->setParameter(':role_id', $data['id']);
            });
        // 删除和角色关联的权限关系
        $this->dbMain()
            ->tblRole2Permission()
            ->delete(function (QueryBuilder $queryBuilder) use ($data) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('role_id', ':role_id'))->setParameter(':role_id', $data['id']);
            });
    }

    /**
     * @param Role $role
     * @param array $fields
     * @return Role
     */
    public function update(Role $role, array $fields)
    {
        foreach ($fields as $key => $value) {
            $role[$key] = $value;
        }
        $role['update_at'] = time();
        $role->update();
        $data = $role->toRawArray();
        $this->cacheUpdate($data);
        return $role;
    }


    /**
     * @param User $user
     * @return Role[]
     */
    public function allByUser(User $user)
    {
        $return = [];
        $user2role = $this->dbMain()
            ->tblUser2Role()
            ->all(function (QueryBuilder $queryBuilder) use ($user) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('user_id', ':user_id'))->setParameter(':user_id', $user->id);
            });
        foreach ($user2role as $row) {
            $role = $this->oneById($row['role_id']);
            if ($role) {
                $return[] = $role;
            }
        }
        return $return;
    }

    /**
     * @param Role $role
     * @return array
     */
    public function permissionsByRole(Role $role)
    {
        $return = [];
        $role2permission = $this->dbMain()
            ->tblRole2Permission()
            ->all(function (QueryBuilder $queryBuilder) use ($role) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('role_id', ':role_id'))->setParameter(':role_id', $role->id);
            });
        foreach ($role2permission as $row) {
            $return[] = $row['permission_id'];
        }
        return $return;
    }
}