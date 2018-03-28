<?php

namespace TCG\Bundle\CMF\Database\MySQL\Model;

use TCG\Component\Database\MySQL\Model;

/**
 * Class User2Role
 * @package TCG\Bundle\CMF\Database\MySQL\Model
 * @property $userId
 * @property $roleId
 * @property $createAt
 */
class User2Role extends Model
{
    protected $userId;
    protected $roleId;
    protected $createAt;
}