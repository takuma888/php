<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;


use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionIndex extends UserController
{
    public function exec()
    {
        // 查询出管理员用户
        $request = $this->getRequest();
        $searchColumns = [
            'id', 'username', 'name',
        ];
        $orderColumns = [
            'id', 'username', 'name', 'create_at'
        ];
        if ($request->isXmlHttpRequest()) {

            $gets = $this->getRequest()->query;
            $draw = $gets->get('draw');
            $start = $gets->get('start');
            $length = $gets->get('length');

            if ($length == -1) {
                // all
                $page = 1;
                $size = PHP_INT_MAX;
            } else {
                $page = max(1, intval($start / $length) + 1);
                $size = $length;
            }

            $sort = $gets->get('order', []);
            $filter = $gets->get('search', []);
            $searchValue = isset($filter['value']) ? trim($filter['value']) : '';
            $rows = [];
            $recordsTotal = 0;
            $recordsFiltered = 0;
            $error = false;

            try {
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
                    ->paginate($page, $size, function (QueryBuilder $queryBuilder) use ($userIds, $orderColumns, $sort, $searchColumns, $searchValue) {
                        if ($searchValue) {
                            $orX = $queryBuilder->expr()->orX();
                            foreach ($searchColumns as $key) {
                                $orX->add($queryBuilder->expr()->like("`{$key}`", ":{$key}"));
                                $queryBuilder->setParameter(":{$key}", "%{$searchValue}%");
                            }
                            $queryBuilder->andWhere($orX);
                            if ($userIds) {
                                $queryBuilder->andWhere($queryBuilder->expr()->in('`id`', $userIds));
                            } else {
                                $queryBuilder->andWhere('false');
                            }
                        } else {
                            if ($userIds) {
                                $queryBuilder->andWhere($queryBuilder->expr()->in('`id`', $userIds));
                            } else {
                                $queryBuilder->andWhere('false');
                            }
                        }
                        foreach ($sort as $item) {
                            $key = $item['column'];
                            if (isset($orderColumns[$key])) {
                                $key = $orderColumns[$key];
                                $dir = $item['dir'];
                                $queryBuilder->addOrderBy("`{$key}`", $dir == 'desc' ? 'DESC': null);
                            }
                        }
                    });
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
                            $roleUrl = $this->url('dashboard.role.index', [
                                'id' => $roleId,
                            ]);
                            $userRoles[] = '<a href="' . $roleUrl . '">' . $role['name'] . '</a>';
                        }
                    }
                    $line['roles'] = implode('&nbsp;&nbsp;', $userRoles);
                    // 操作
                    $operations = [];
                    // 修改角色 重置密码 删除
                    if ($this->getAccount()->hasPermission('dashboard.user.edit')) {
                        $editUrl = $this->url('dashboard.user.edit', [
                            'id' => $row['id'],
                        ]);
                        $operations[] = '<a href="javascript:void(0);" data-toggle="layer" data-type="modal" data-width="500px" data-url="' . $editUrl . '"><i class="fa fa-edit"></i> 修改角色</a>';
                    }
                    if ($this->getAccount()->hasPermission('dashboard.user.password')) {
                        $passwordUrl = $this->url('dashboard.user.password', [
                            'id' => $row['id'],
                        ]);
                        $operations[] = '<a href="javascript:void(0);" data-toggle="layer" data-type="modal" data-width="500px" data-url="' . $passwordUrl . '"><i class="fa fa-key"></i> 修改密码</a>';
                    }
                    if ($this->getAccount()->hasPermission('dashboard.user.delete')) {
                        $deleteUrl = $this->url('dashboard.user.delete', [
                            'ids' => [$row['id']]
                        ]);
                        $operations[] = '<a href="javascript:void(0);" data-url="' . $deleteUrl . '" data-toggle="layer" data-type="modal" data-width="500px"><i class="fa fa-remove"></i> 删除</a>';
                    }
                    $line['op'] = implode('&nbsp;&nbsp;', $operations);
                    $rows[] = $line;
                }
                $recordsTotal = $pager->getCount();
                $recordsFiltered = $pager->getCount();
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            $data = [
                'draw' => $draw,
                'data' => $rows,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
            ];
            if ($error) {
                $data['error'] = $error;
            }
            return $this->json($data);
        }

        return $this->render('user/index.html.twig');
    }
}