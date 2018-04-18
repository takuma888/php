<?php

namespace TCG\Module\CMS\Dashboard\Controller\UserController;

use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\CMSException;
use TCG\Module\CMS\Dashboard\Controller\UserController;

class ActionAdd extends UserController
{
    public function exec()
    {
        $request = $this->getRequest();

        // 获取所有的后台角色列表
        $rootRole = $this->tcgCMF()
            ->dbMain()
            ->tblRoles()
            ->getRootNode();
        $rootRole = $this->tcgCMF()
            ->dbMain()
            ->tblRoles()
            ->model($rootRole);
        $roles = $rootRole->getSubTree();
        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[] = $role['id'];
        }

        if ($request->isXmlHttpRequest() && $request->query->get('action') == 'users') {
            $user2roles = $this->tcgCMF()
                ->dbMain()
                ->tblUser2Role()
                ->all(function (QueryBuilder $queryBuilder) use ($roleIds) {
                    if ($roleIds) {
                        $queryBuilder->andWhere($queryBuilder->expr()->in('`role_id`', $roleIds));
                    } else {
                        $queryBuilder->andWhere('false');
                    }
                });
            $userIds = [];
            foreach ($user2roles as $user2role) {
                $userIds[] = $user2role['user_id'];
            }

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
                $searchColumns = [
                    'id', 'username', 'name',
                ];
                $orderColumns = [
                    1 => 'id', 2 => 'username', 3 => 'name', 4 =>'create_at'
                ];

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
                                $queryBuilder->andWhere($queryBuilder->expr()->notIn('`id`', $userIds));
                            } else {
                                $queryBuilder->andWhere('false');
                            }
                        } else {
                            if ($userIds) {
                                $queryBuilder->andWhere($queryBuilder->expr()->notIn('`id`', $userIds));
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
                        'op' => '<div class="tui-control-checkbox">
                        <input type="checkbox" name="users[' . $row['id'] . ']" value="' . $row['id'] . '" class="tui-checkbox table-item-check" id="users-item-check-' . $row['id'] . '">
                        <label class="tui-label" for="users-item-check-' . $row['id'] . '"></label>
                    </div>',
                        'id' => $row['id'],
                        'username' => '<span title="' . $row['username'] . '">' . $row['username'] . '</span>',
                        'name' => '<span title="' . $row['name'] . '">' . $row['name'] . '</span>',
                        'create_at' => date('Y-m-d H:i:s', $row['create_at']),
                    ];
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


        if ($request->getMethod() == 'POST') {
            $session = $request->getSession();
            $posts = $request->request;

            $oldRoleIds = $roleIds;
            $roleIds = $posts->get('roles', []);
            $userIds = $posts->get('users', []);
            try {
                if (!$userIds) {
                    throw new CMSException("没有选择用户");
                }
                if (!$roleIds) {
                    throw new CMSException("没有选择角色");
                }
                $this->tcgCMF()
                    ->dbMain()
                    ->transaction((function () use ($oldRoleIds, $roleIds, $userIds) {
                        // 先清除这些用户的后台角色
                        $this->tcgCMF()
                            ->dbMain()
                            ->tblUser2Role()
                            ->delete(function (QueryBuilder $queryBuilder) use ($oldRoleIds, $userIds) {
                                $queryBuilder->andWhere($queryBuilder->expr()->in('`role_id`', $oldRoleIds));
                                $queryBuilder->andWhere($queryBuilder->expr()->in('`user_id`', $userIds));
                            });
                        // 创建新的用户角色关系
                        $user2roles = [];
                        foreach ($userIds as $userId) {
                            foreach ($roleIds as $roleId) {
                                $user2roles[] = [
                                    'user_id' => $userId,
                                    'role_id' => $roleId,
                                ];
                            }
                        }
                        if ($user2roles) {
                            $this->tcgCMF()
                                ->dbMain()
                                ->tblUser2Role()
                                ->multiInsert($user2roles);
                        }
                    })->bindTo($this));

                $session->getFlashBag()->add('success', '操作成功');
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', $e->getMessage());
            }
            return $this->redirect('dashboard.user.index');
        }
        return $this->render('user/add.html.twig', [
            'roles' => $roles,
        ]);
    }
}