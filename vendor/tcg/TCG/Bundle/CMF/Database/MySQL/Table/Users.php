<?php

namespace TCG\Bundle\CMF\Database\MySQL\Table;


use TCG\Bundle\CMF\Database\MySQL\Model\User;
use TCG\Component\Database\MySQL\Table;

class Users extends Table
{

    /**
     * @return string
     */
    public function getCreateTableSQL()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `username` VARCHAR(32) DEFAULT NULL COMMENT '用户名',
  `email` VARCHAR(128) DEFAULT NULL COMMENT '邮箱地址',
  `mobile` VARCHAR(16) DEFAULT NULL COMMENT '手机号',
  `password` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
  `qq` VARCHAR(16) NOT NULL DEFAULT '' COMMENT 'QQ',
  `wechat` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '微信',
  `name` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '名字',
  `avatar` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '头像',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `login_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '今日登录时间',
  `session_id` VARBINARY(128) NOT NULL DEFAULT '' COMMENT 'SESSION ID',
  PRIMARY KEY (`id`),
  UNIQUE (`username`),
  UNIQUE (`email`),
  UNIQUE (`mobile`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;
        return $sql;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return [
            'id' => 0,
            'username' => '',
            'email' => '',
            'mobile' => '',
            'password' => '',
            'qq' => '',
            'wechat' => '',
            'name' => '',
            'avatar' => '',
            'create_at' => 0,
            'update_at' => 0,
            'login_at' => 0,
        ];
    }

    /**
     * @param array $fields
     * @return User
     */
    public function model(array $fields = [])
    {
        return new User($this, $fields);
    }
}