<?php

namespace TCG\Bundle\CMF\Database\MySQL\Model;


use TCG\Component\Database\MySQL\Model;

/**
 * Class User
 * @package TCG\Bundle\CMF\Database\MySQL\Model
 * @property $username
 * @property $email
 * @property $mobile
 * @property $password
 * @property $qq
 * @property $wechat
 * @property $name
 * @property $avatar
 * @property $createAt
 * @property $updateAt
 * @property $loginAt
 */
class User extends Model
{
    protected $username;
    protected $email;
    protected $mobile;
    protected $password;
    protected $qq;
    protected $wechat;
    protected $name;
    protected $avatar;
    protected $createAt;
    protected $updateAt;
    protected $loginAt;

}