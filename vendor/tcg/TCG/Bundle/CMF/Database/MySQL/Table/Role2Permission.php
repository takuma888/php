<?php

namespace TCG\Bundle\CMF\Database\MySQL\Table;

use TCG\Component\Database\MySQL\Table;

class Role2Permission extends Table
{
    public function getCreateTableSQL()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `role_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `permission_id` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '权限ID',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `role` (`role_id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;
        return $sql;
    }

    public function getFields()
    {
        return [
            'role_id' => 0,
            'permission_id' => '',
            'create_at' => 0,
        ];
    }

    public function model(array $fields = [])
    {
    }
}