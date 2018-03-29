<?php

namespace TCG\Bundle\CMF\DataProvider;

use TCG\Bundle\CMF\Database\MySQL\Model\User;
use TCG\Component\Database\MySQL\Query\QueryBuilder;

class UserProvider extends AbstractProvider
{
    /**
     * @param $key
     * @param $value
     * @return bool|mixed|null|string|\TCG\Bundle\CMF\Database\MySQL\Model\User
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
                    ->tblUsers()
                    ->model($data);
            }
        }
        return $data;
    }

    /**
     * @param $id
     * @return bool|mixed|null|string|\TCG\Bundle\CMF\Database\MySQL\Model\User
     */
    public function oneById($id)
    {
        return $this->oneBy('id', $id);
    }


    public function oneByFromDb($key, $value)
    {
        return $this->dbMain()
            ->tblUsers()
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
        if ($data['username'] && !in_array('username', $excludes)) {
            $this->oneByToCache('username', $data['username'], $data);
        }
        if ($data['email'] && !in_array('email', $excludes)) {
            $this->oneByToCache('email', $data['email'], $data);
        }
        if ($data['mobile'] && !in_array('mobile', $excludes)) {
            $this->oneByToCache('mobile', $data['mobile'], $data);
        }
        if (!in_array('id', $excludes)) {
            $this->oneByToCache('id', $data['id'], $data);
        }
    }


    public function cacheClear($data)
    {
        if ($data['username']) {
            $this->oneByClearCache('username', $data['username']);
        }
        if ($data['email']) {
            $this->oneByClearCache('email', $data['email']);
        }
        if ($data['mobile']) {
            $this->oneByClearCache('mobile', $data['mobile']);
        }
        $this->oneByClearCache('id', $data['id']);
    }



    public function insert(User $user)
    {
        $now = time();
        $user['create_at'] = $now;
        $user->insert();
        $data = $user->toRawArray();
        $this->cacheUpdate($data);
        return $user;
    }


    /**
     * @param User $user
     * @param array $fields
     * @return User
     */
    public function update(User $user, array $fields)
    {
        foreach ($fields as $field => $value) {
            $user[$field] = $value;
        }
        $user['update_at'] = time();
        $user->update();
        $data = $user->toRawArray();
        $this->cacheUpdate($data);
        return $user;
    }

    /**
     * @param User $user
     * @return User
     */
    public function merge(User $user)
    {
        $user['update_at'] = time();
        $user->merge();
        $data = $user->toRawArray();
        $this->cacheUpdate($data);
        return $user;
    }

    /**
     * @param User $user
     */
    public function remove(User $user)
    {
        // 删除用户
        $user->remove();
        $data = $user->toRawArray();
        $this->cacheClear($data);
        // 删除与用户关联的角色关系
        $this->dbMain()
            ->tblUser2Role()
            ->delete(function (QueryBuilder $queryBuilder) use ($data) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq('user_id', ':user_id'))->setParameter(':user_id', $data['id']);
            });
    }
}