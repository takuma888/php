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

    /**
     * @param $nodeId
     * @param $rootValue
     * @return array
     */
    public function getSubTree($nodeId, $rootValue)
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
        and `node`.`id` = :node_id
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
                ':node_id' => $nodeId,
                ':root' => $rootValue,
            ]);
        return $stmt->all();
    }


    /**
     * @param $nodeId
     * @param $rootValue
     * @return array
     */
    public function getDirectChildNodes($nodeId, $rootValue)
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
        and `node`.`id` = :node_id
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
having `depth` = 1
order by `node`.`left_value`
SQL;

        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->slave()
            ->statement($sql, [
                ':node_id' => $nodeId,
                ':root' => $rootValue,
            ]);
        return $stmt->all();
    }

    /**
     * @param $nodeId
     * @param $rootValue
     * @return array
     */
    public function getPath($nodeId, $rootValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
select `parent`.*, (`parent`.`right_value` - `parent`.`left_value`) as `width`
from {@table} as `node`, {@table} as `parent`
where `node`.`left_value` between `parent`.`left_value` and `parent`.`right_value`
and `node`.`id` = :node_id
and `node`.`root_value` = :root
and `parent`.`root_value` = :root
order by `parent`.`left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->slave()
            ->statement($sql, [
                ':node_id' => $nodeId,
                ':root' => $rootValue,
            ]);
        return $stmt->all();
    }

    /**
     * @param $nodeId
     * @param $rootValue
     * @return array
     */
    public function getDirectParentNode($nodeId, $rootValue) 
    {
        $path = $this->getPath($nodeId, $rootValue);
        $reversePath = array_reverse($path);
        return isset($reversePath[1]) ? $reversePath[1] : null;
    }

    /**
     * @param array $fields
     * @param $parentRightValue
     * @param $rootValue
     * @return string
     */
    public function insertChildNode(array $fields, $parentRightValue, $rootValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
update {@table} set `right_value` = `right_value` + 2 where `right_value` >= :parent_right_value and `root_value` = :root;
update {@table} set `left_value` = `left_value` + 2 where `left_value` > :parent_right_value and `root_value` = :root;
SQL;
        $insertQuery = $this->getInsertQuery($fields);
        $sql .= "\n" . $insertQuery->getSQL();
        $params = $insertQuery->getParameters();
        $params[':parent_right_value'] = $parentRightValue;
        $params[':root'] = $rootValue;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->master()
            ->statement($sql, $params);
        return $stmt->getLastInsertId();
    }

    /**
     * @param $nodeLeftValue
     * @param $nodeRightValue
     * @param $rootValue
     */
    public function removeNode($nodeLeftValue, $nodeRightValue, $rootValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
delete from {@table} where `left_value` = :node_left and `root_value` = :root;
update {@table} set `right_value` = `right_value` - 1, `left_value` = `left_value` - 1 where `left_value` between :node_left and :node_right and `root_value` = :root;
update {@table} set `right_value` = `right_value` - 2 where `right_value` > :node_right and `root_value` = :root;
update {@table} set `left_value` = `left_value` - 2 where `left_value` > :node_right and `root_value` = :root;
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $this->getClient()->master()
            ->statement($sql, [
                ':node_left' => $nodeLeftValue,
                ':node_right' => $nodeRightValue,
                ':root' => $rootValue,
            ]);
    }

    /**
     * @param $nodeLeftValue
     * @param $nodeRightValue
     * @param $rootValue
     */
    public function removeRecursiveNodes($nodeLeftValue, $nodeRightValue, $rootValue)
    {
        $nodeWidth = $nodeRightValue - $nodeLeftValue + 1;
        $tableName = $this->getName();
        $sql = <<<SQL
delete from {@table} where `left_value` between :node_left and :node_right and `root_value` = :root;
update {@table} set `right_value` = `right_value` - :node_width where `right_value` > :node_right and `root_value` = :root;
update {@table} set `left_value` = `left_value` - :node_width where `left_value` > :node_right and `root_value` = :root;
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $this->getClient()->master()
            ->statement($sql, [
                ':node_left' => $nodeLeftValue,
                ':node_right' => $nodeRightValue,
                ':node_width' => $nodeWidth,
                ':root' => $rootValue,
            ]);
    }
}