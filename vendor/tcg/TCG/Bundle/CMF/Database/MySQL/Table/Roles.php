<?php

namespace TCG\Bundle\CMF\Database\MySQL\Table;

use TCG\Bundle\CMF\Database\MySQL\Model\Role;
use TCG\Component\Database\MySQL\PreOrderTree\Table;

class Roles extends Table
{
    public function getCreateTableSQL()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `key` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `type` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '类型',
  `description` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '描述',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `left_value` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预排续树左值',
  `right_value` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预排续树右值',
  PRIMARY KEY (`id`),
  UNIQUE (`key`),
  KEY `tree_left` (`left_value`),
  KEY `tree_right` (`right_value`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;
        return $sql;
    }

    public function getFields()
    {
        return [
            'id' => 0,
            'key' => '',
            'name' => '',
            'type' => '',
            'description' => '',
            'create_at' => 0,
            'update_at' => 0,
            'left_value' => 0,
            'right_value' => 0,
        ];
    }

    public function model(array $fields = [])
    {
        return new Role($this, $fields);
    }
}