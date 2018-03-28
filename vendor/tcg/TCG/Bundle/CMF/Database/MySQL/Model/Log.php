<?php

namespace TCG\Bundle\CMF\Database\MySQL\Model;

use TCG\Component\Database\MySQL\Model;

/**
 * Class Log
 * @package TCG\Bundle\CMF\Database\MySQL\Model
 * @property $userId
 * @property $name
 * @property $date
 * @property $route
 * @property $method
 * @property $input
 * @property $output
 * @property $info
 * @property $createAt
 */
class Log extends Model
{
    protected $userId;
    protected $name;
    protected $date;
    protected $route;
    protected $method;
    protected $input;
    protected $output;
    protected $info;
    protected $createAt;
}