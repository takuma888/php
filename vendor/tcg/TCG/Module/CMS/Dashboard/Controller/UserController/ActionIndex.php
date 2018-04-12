<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;


use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionIndex extends UserController
{
    public function exec()
    {
        // 查询出管理员用户
        $gets = $this->getRequest()->query;
        $page = $gets->get('page', 1);
        $page = max(1, intval($page));
        $size = 25;
        $sort = $gets->get('sort', []);
        $filter = $gets->get('filter', []);

        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $results = $this->tcgCMF()
                ->dbMain()
                ->tblRoles()
                ->all(function (QueryBuilder $queryBuilder) {
                    $queryBuilder->andWhere($queryBuilder->expr()->eq('`type`', ':type'))->setParameter(':type', 'admin');
                });
            $roleIds = [];
            $roles = [];
            foreach ($results as $role) {
                $roleIds[] = $role['id'];
                $roles[$role['id']] = $role;
            }
            $userIds = [];
            $results = $this->tcgCMF()
                ->dbMain()
                ->tblUser2Role()
                ->all(function (QueryBuilder $queryBuilder) use ($roleIds) {
                    $queryBuilder->andWhere($queryBuilder->expr()->in('`role_id`', $roleIds));
                });
            $user2roles = [];
            foreach ($results as $user2role) {
                $userIds[] = $user2role['user_id'];
                if (!isset($user2roles[$user2role['user_id']])) {
                    $user2roles[$user2role['user_id']] = [];
                }
                $user2roles[$user2role['user_id']][] = $user2role['role_id'];
            }

            $pager = $this->tcgCMF()
                ->dbMain()
                ->tblUsers()
                ->paginate($page, $size, function (QueryBuilder $queryBuilder) use ($userIds, $sort, $filter) {
                    $queryBuilder->andWhere($queryBuilder->expr()->in('`id`', $userIds));
                    foreach ($filter as $item) {
                        $key = $item['key'];
                        $value = trim($item['value']);
                        if ($value !== '') {
                            $queryBuilder->andWhere($queryBuilder->expr()->eq("`{$key}`", ":{$key}"))->setParameter(":{$key}", $value);
                        }
                    }
                    foreach ($sort as $item) {
                        $key = $item['key'];
                        $dir = $item['dir'];
                        $queryBuilder->addOrderBy("`{$key}`", $dir == 'desc' ? 'DESC': null);
                    }
                });

            $rows = [];

            foreach ($pager->getData() as $row) {
                $line = [];
                $line += [
                    'id' => $row['id'],
                    'username' => '<span title="' . $row['username'] . '">' . $row['username'] . '</span>',
                    'name' => '<span title="' . $row['name'] . '">' . $row['name'] . '</span>',
                    'create_at' => date('Y-m-d H:i:s', $row['create_at']),
                ];

                // 角色
                $userRoles = [];
                if (isset($user2roles[$row['id']])) {
                    foreach ($user2roles[$row['id']] as $roleId) {
                        $role = $roles[$roleId];
                        $userRoles[] = '<a href="javascript:void(0);">' . $role['name'] . '</a>';
                    }
                }
                $line['roles'] = implode('&nbsp;&nbsp;', $userRoles);
                // 操作
                $operations = [];
                // 修改， 删除
                if ($this->getAccount()->hasPermission('dashboard.user.edit')) {
                    $editUrl = $this->url('dashboard.user.edit', [
                        'id' => $row['id'],
                    ]);
                    $operations[] = '<a href="' . $editUrl . '"><i class="fa fa-edit"></i> 修改</a>';
                }
                if ($this->getAccount()->hasPermission('dashboard.user.delete')) {
                    $deleteUrl = $this->url('dashboard.user.delete', [
                        'ids' => [$row['id']]
                    ]);
                    $operations[] = '<a href="javascript:void(0);" data-url="' . $deleteUrl . '" data-toggle="layer" data-type="iframe"><i class="fa fa-remove"></i> 删除</a>';
                }
                $line['op'] = implode('&nbsp;&nbsp;', $operations);
                $rows[] = $line;
            }

            $data = [
                'page' => $page,
                'size' => $size,
                'total' => $pager->getCount(),
                'data' => $rows,
            ];

            if ($sort) {
                $data['sort'] = $sort;
            }
            if ($filter) {
                $data['filter'] = $filter;
            }
            return $this->json($data);
        }

        return $this->render('user/index.html.twig');
    }
}