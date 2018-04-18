<?php


namespace TCG\Module\CMS\Service;

use TCG\Bundle\CMF\PublicTrait as CMFTrait;
use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Module\CMS\PrivateTrait;

class SessionService
{
    use PrivateTrait;
    use CMFTrait;


    public function kickUserLoginByUserIds(array $userIds)
    {
        $users = $this->tcgCMF()
            ->dbMain()
            ->tblUsers()
            ->all(function (QueryBuilder $queryBuilder) use ($userIds) {
                if ($userIds) {
                    $queryBuilder->where($queryBuilder->expr()->in('`id`', $userIds));
                } else {
                    $queryBuilder->where('false');
                }
            });
        $sessionIds = [];
        foreach ($users as $user) {
            $sessionIds[] = $user['session_id'];
        }
        // 清除session
        $this->dbMain()
            ->tblSessions()
            ->delete(function (QueryBuilder $queryBuilder) use ($sessionIds) {
                if ($sessionIds) {
                    foreach ($sessionIds as $i => $sessionId) {
                        $queryBuilder->orWhere($queryBuilder->expr()->eq('`session_id`', ':session_id_' . $i))->setParameter(':session_id_' . $i, $sessionId);
                    }
                } else {
                    $queryBuilder->where('false');
                }
            });
    }



    public function kickUserLoginByRoleIds(array $roleIds)
    {
        $user2roles = $this->tcgCMF()
            ->dbMain()
            ->tblUser2Role()
            ->all(function (QueryBuilder $queryBuilder) use ($roleIds) {
                if ($roleIds) {
                    $queryBuilder->where($queryBuilder->expr()->in('`role_id`', $roleIds));
                } else {
                    $queryBuilder->where('false');
                }
            });
        $userIds = [];
        foreach ($user2roles as $user2role) {
            $userIds[] = $user2role['user_id'];
        }
        $userIds = array_unique($userIds);
        $this->kickUserLoginByUserIds($userIds);
    }
}