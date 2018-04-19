<?php

namespace TCG\Module\CMS\Dashboard\Controller\RoleController;


use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\Dashboard\Controller\RoleController;

class ActionIndex extends RoleController
{
    public function exec()
    {
        $request = $this->getRequest();
        // 当前角色
        $roleId = $request->query->get('id', 0);
        if (!$roleId) {
            $rootRole = $this->tcgCMF()
                ->serviceRole()
                ->getRoot();
        } else {
            $rootRole = $this->tcgCMF()
                ->serviceRole()
                ->getNode($roleId);
        }
        $roleTree = $this->tcgCMF()->serviceRole()
            ->roleTree($rootRole);
        // 子角色
        $roles = $roleTree->getRoot()->children;


        // 类似面包屑导航

        $rolePath = $rootRole->getPath();

        // 当前角色的用户
        if ($request->isXmlHttpRequest() &&$request->query->get('action') == 'user') {
            $result = $this->tcgCMF()
                ->dbMain()
                ->tblUser2Role()
                ->all(function (QueryBuilder $queryBuilder) use ($rootRole) {
                    $queryBuilder->where($queryBuilder->expr()->eq('`role_id`', ':role_id'))->setParameter(':role_id', $rootRole->id);
                });
            $userIds = [];
            foreach ($result as $user2role) {
                $userIds[] = $user2role['user_id'];
            }

            $searchColumns = [
                'id', 'username', 'name',
            ];
            $orderColumns = [
                'id', 'username', 'name', 'create_at'
            ];

            $gets = $request->query;
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

                    // 操作
                    $operations = [];
                    if ($this->getAccount()->hasPermission('dashboard.role.delete_users')) {
                        $deleteUrl = $this->url('dashboard.role.delete_users', [
                            'user_ids' => [$row['id']],
                            'id' => $roleId,
                        ]);
                        $operations[] = '<a data-url="' . $deleteUrl . '" href="javascript:void(0);" data-toggle="layer" data-type="modal" data-width="500px"><i class="fa fa-remove"></i> 移除用户</a>';
                        $line['op'] = implode('&nbsp;&nbsp;', $operations);
                    }
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


        // 角色权限
        $rolePermissions = $this->tcgCMF()
            ->providerRole()
            ->permissionsByRole($rootRole);


        return $this->render('role/index.html.twig', [
            'roles' => $roles,
            'root' => $rootRole,
            'role_path' => $rolePath,
            'role_permissions' => $rolePermissions,
        ]);
    }
}