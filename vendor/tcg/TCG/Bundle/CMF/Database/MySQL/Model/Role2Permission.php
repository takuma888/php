<?php

namespace TCG\Bundle\CMF\Database\MySQL\Model;

use TCG\Component\Database\MySQL\Model;

/**
 * Class Role2Permission
 * @package TCG\Bundle\CMF\Database\MySQL\Model
 * @property $roleId
 * @property $permissionId
 * @property $createAt
 */
class Role2Permission extends Model
{
    protected $roleId;
    protected $permissionId;
    protected $createAt;
}