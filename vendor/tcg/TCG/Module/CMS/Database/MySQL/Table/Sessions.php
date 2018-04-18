<?php

namespace TCG\Module\CMS\Database\MySQL\Table;

use TCG\Component\Database\MySQL\Table;

class Sessions extends Table
{
    public function getCreateTableSQL()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {@table} (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `session_id` VARBINARY(128) NOT NULL COMMENT 'SESSION ID',
  `data` BLOB NOT NULL COMMENT '数据',
  `lifetime` MEDIUMINT NOT NULL COMMENT '生命周期时间',
  `timestamp` INTEGER UNSIGNED NOT NULL COMMENT '创建的时间戳',
  PRIMARY KEY (`id`),
  UNIQUE (`session_id`)
) ENGINE InnoDB DEFAULT CHARSET utf8 COLLATE utf8_bin;
SQL;
        return $sql;
    }

    public function getFields()
    {
        return [
            'id' => 0,
            'session_id' => '',
            'data' => '',
            'lifetime' => 0,
            'timestamp' => 0,
        ];
    }


    public function model(array $fields = [])
    {
        return null;
    }
}