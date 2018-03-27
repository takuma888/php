<?php

namespace TCG\Component\Database\MySQL\PreOrderForest;

use TCG\Component\Database\MySQL\Table as BaseTable;

abstract class Table extends BaseTable
{
    public function getFields()
    {
        return [
            'id' => 0,
            'left_value' => 0,
            'right_value' => 0,
            'root_value' => 0,
        ];
    }


    /**
     * @param $nodeId
     * @return null|array
     */
    public function getNode($nodeId)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
SELECT *
FROM {@table}
WHERE `id` = :id
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->slave()
            ->statement($sql, [
                ':id' => $nodeId
            ]);
        return $stmt->one();
    }

    /**
     * @param int $rootValue
     * @return array
     */
    public function getAllLeafNodes($rootValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
SELECT * 
FROM {@table} 
WHERE `right_value` = `left_value` + 1 AND `root_value` = :root_value
ORDER BY `left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()
            ->slave()
            ->statement($sql, [
                ':root_value' => $rootValue,
            ]);
        return $stmt->all();
    }


    /**
     * @param int $rootValue
     * @return null|array
     */
    public function getRootNode($rootValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
SELECT *, (`right_value` - `left_value`) AS `width` 
FROM {@table} 
WHERE `left_value` = 0 AND `root_value` = :root_value 
LIMIT 1
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->slave()
            ->statement($sql, [
                ':root_value' => $rootValue,
            ]);
        return $stmt->one();
    }


    /**
     * @return array
     */
    public function getRootNodes()
    {
        $tableName = $this->getName();
        $sql = <<<SQL
SELECT *, (`right_value` - `left_value`) AS `width` 
FROM {@table} 
WHERE `left_value` = 0
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->slave()
            ->statement($sql);
        return $stmt->all();
    }

    /**
     * @param $rootValue
     * @return array
     */
    public function getTree($rootValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
select `node`.*, (count(`parent`.`id`) - (`sub_tree`.`depth` + 1)) as `depth`, (`node`.`right_value` - `node`.`left_value`) as `width`
from {@table} as `node`,
     {@table} as `parent`,
     {@table} as `sub_parent`,
     (
        select `node`.*, (count(`parent`.`id`) - 1) as `depth`
        from {@table} as `node`, {@table} as `parent`
        where `node`.`left_value` between `parent`.`left_value` and `parent`.`right_value`
        and `node`.`left_value` = 0
        and `node`.`root_value` = :root
        and `parent`.`root_value` = :root
        group by `node`.`id`
        order by `node`.`left_value`
     ) as `sub_tree`
where `node`.`left_value` between `parent`.`left_value` and `parent`.`right_value`
and `node`.`left_value` between `sub_parent`.`left_value` and `sub_parent`.`right_value`
and `sub_parent`.`id` = `sub_tree`.`id`
and `node`.`root_value` = :root
and `parent`.`root_value` = :root
and `sub_parent`.`root_value` = :root
and `sub_tree`.`root_value` = :root
group by `node`.`id`
order by `node`.`left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->slave()
            ->statement($sql, [
                ':root' => $rootValue,
            ]);
        return $stmt->all();
    }


    public function getSubTree($nodeId, $rootValue)
    {

    }
}