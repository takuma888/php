<?php

namespace TCG\Bundle\CMF\Database\MySQL\Table;

use TCG\Component\Database\MySQL\Table;

class Logs extends Table
{
    public function getCreateTableSQL()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '用户名称',
  `date` DATE NOT NULL DEFAULT '1970-01-01' COMMENT '日期',
  `route` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '路由',
  `method` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '方法，GET，POST等',
  `input` MEDIUMTEXT COMMENT '输入',
  `output` MEDIUMTEXT COMMENT '输出',
  `info` LONGTEXT COMMENT '调试信息',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;
        return $sql;
    }

    public function getFields()
    {
        return [
            'user_id' => 0,
            'name' => '',
            'date' => '',
            'route' => '',
            'method' => '',
            'input' => '',
            'output' => '',
            'info' => '',
            'create_at' => 0,
        ];
    }

    public function model(array $fields = [])
    {
    }
}