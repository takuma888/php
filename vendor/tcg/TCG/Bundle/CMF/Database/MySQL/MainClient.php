<?php

namespace TCG\Bundle\CMF\Database\MySQL;

use TCG\Bundle\CMF\Database\MySQL\Table\Role2Permission;
use TCG\Bundle\CMF\Database\MySQL\Table\Roles;
use TCG\Bundle\CMF\Database\MySQL\Table\User2Role;
use TCG\Bundle\CMF\Database\MySQL\Table\Users;
use TCG\Component\Database\MySQL\Client;

class MainClient extends Client
{
    /**
     * @return Users
     */
    public function tblUsers()
    {
        return getContainer()->get('tcg_bundle.cmf.mysql.main.table.users');
    }

    /**
     * @return Roles
     */
    public function tblRoles()
    {
        return getContainer()->get('tcg_bundle.cmf.mysql.main.table.roles');
    }

    /**
     * @return User2Role
     */
    public function tblUser2Role()
    {
        return getContainer()->get('tcg_bundle.cmf.mysql.main.table.user2role');
    }

    /**
     * @return Role2Permission
     */
    public function tblRole2Permission()
    {
        return getContainer()->get('tcg_bundle.cmf.mysql.main.table.role2permission');
    }
}