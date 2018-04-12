<?php

namespace TCG\Bundle\CMF\Database\MySQL\Model;

use TCG\Component\Database\MySQL\PreOrderTree\Model;

/**
 * Class Role
 * @package TCG\Bundle\CMF\Database\MySQL\Model
 * @property $key
 * @property $name
 * @property $type
 * @property $description
 * @property $createAt
 * @property $updateAt
 */
class Role extends Model
{
    protected $key;
    protected $name;
    protected $type;
    protected $description;
    protected $createAt;
    protected $updateAt;
}