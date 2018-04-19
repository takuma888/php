<?php

namespace TCG\Bundle\CMF\Database\MySQL\Table;

use TCG\Component\Database\MySQL\Table;

class User2Role extends Table
{
    public function getCreateTableSQL()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `role_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `create_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_role` (`user_id`, `role_id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
SQL;
        return $sql;
    }

    public function getFields()
    {
        return [
            'user_id' => 0,
            'role_id' => 0,
            'create_at' => 0,
        ];
    }


    public function model(array $fields = [])
    {
        return new \TCG\Bundle\CMF\Database\MySQL\Model\User2Role($this, $fields);
    }
}