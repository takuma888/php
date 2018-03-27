<?php

namespace TCG\Component\Database\MySQL\PreOrderTree;

use TCG\Component\Database\MySQL\Table as BaseTable;

abstract class Table extends BaseTable
{

    public function getFields()
    {
        return [
            'id' => 0,
            'left_value' => 0,
            'right_value' => 0,
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
     * @return array
     */
    public function getAllLeafNodes()
    {
        $tableName = $this->getName();
        $sql = <<<SQL
SELECT * 
FROM {@table} 
WHERE `right_value` = `left_value` + 1 
ORDER BY `left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()
            ->slave()
            ->statement($sql);
        return $stmt->all();
    }

    /**
     * @return null|array
     */
    public function getRootNode()
    {
        $tableName = $this->getName();
        $sql = <<<SQL
SELECT *, (`right_value` - `left_value`) AS `width` 
FROM {@table} 
WHERE `left_value` = 0 LIMIT 1
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()->slave()
            ->statement($sql);
        return $stmt->one();
    }

    /**
     * @return array
     */
    public function getTree()
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
        group by `node`.`id`
        order by `node`.`left_value`
     ) as `sub_tree`
where `node`.`left_value` between `parent`.`left_value` and `parent`.`right_value`
and `node`.`left_value` between `sub_parent`.`left_value` and `sub_parent`.`right_value`
and `sub_parent`.`id` = `sub_tree`.`id`
group by `node`.`id`
order by `node`.`left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()
            ->slave()
            ->statement($sql);
        return $stmt->all();
    }

    /**
     * @param $nodeId
     * @return array|null
     */
    public function getDirectParentNode($nodeId)
    {
        $path = $this->getPath($nodeId);
        $reversePath = array_reverse($path);
        return isset($reversePath[1]) ? $reversePath[1] : null;
    }

    /**
     * @param $parentNodeId
     * @return array
     */
    public function getSubTree($parentNodeId)
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
        and `node`.`id` = :parent_node_id
        group by `node`.`id`
        order by `node`.`left_value`
     ) as `sub_tree`
where `node`.`left_value` between `parent`.`left_value` and `parent`.`right_value`
and `node`.`left_value` between `sub_parent`.`left_value` and `sub_parent`.`right_value`
and `sub_parent`.`id` = `sub_tree`.`id`
group by `node`.`id`
order by `node`.`left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()
            ->slave()
            ->statement($sql, [
                ':parent_node_id' => $parentNodeId,
            ]);
        return $stmt->all();
    }

    /**
     * @param $parentNodeId
     * @return array
     */
    public function getDirectChildNodes($parentNodeId)
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
        and `node`.`id` = :parent_node_id
        group by `node`.`id`
        order by `node`.`left_value`
     ) as `sub_tree`
where `node`.`left_value` between `parent`.`left_value` and `parent`.`right_value`
and `node`.`left_value` between `sub_parent`.`left_value` and `sub_parent`.`right_value`
and `sub_parent`.`id` = `sub_tree`.`id`
group by `node`.`id`
having `depth` = 1
order by `node`.`left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()
            ->slave()
            ->statement($sql, [
                ':parent_node_id' => $parentNodeId,
            ]);
        return $stmt->all();
    }


    /**
     * @param $nodeId
     * @return array
     */
    public function getPath($nodeId)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
select `parent`.*, (`parent`.`right_value` - `parent`.`left_value`) as `width`
from {@table} as `node`, {@table} as `parent`
where `node`.`left_value` between `parent`.`left_value` and `parent`.`right_value`
and `node`.`id` = :node_id
order by `parent`.`left_value`
SQL;
        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()
            ->slave()
            ->statement($sql, [
                ':node_id' => $nodeId,
            ]);
        return $stmt->all();
    }

    /**
     * @param array $fields
     * @param $parentRightValue
     * @return string
     */
    public function insertChildNode(array $fields, $parentRightValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
update {@table} set `right_value` = `right_value` + 2 where `right_value` >= :parent_right_value;
update {@table} set `left_value` = `left_value` + 2 where `left_value` > :parent_right_value;
SQL;
        $insertQuery = $this->getInsertQuery($fields);
        $sql .= "\n" . $insertQuery->getSQL();
        $params = $insertQuery->getParameters();
        $params[':parent_right_value'] = $parentRightValue;

        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $stmt = $this->getClient()
            ->master()
            ->statement($sql, $params);
        return $stmt->getLastInsertId();
    }


    /**
     * @param $nodeLeftValue
     * @param $nodeRightValue
     */
    public function remove($nodeLeftValue, $nodeRightValue)
    {
        $tableName = $this->getName();
        $sql = <<<SQL
delete from {@table} where `left_value` = :node_left;
update {@table} set `right_value` = `right_value` - 1, `left_value` = `left_value` - 1 where `left_value` between :node_left and :node_right;
update {@table} set `right_value` = `right_value` - 2 where `right_value` > :node_right;
update {@table} set `left_value` = `left_value` - 2 where `left_value` > :node_right;
SQL;

        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $this->getClient()
            ->master()
            ->statement($sql, [
                ':node_left' => $nodeLeftValue,
                ':node_right' => $nodeRightValue,
            ]);
    }

    /**
     * @param $nodeLeftValue
     * @param $nodeRightValue
     */
    public function removeRecursive($nodeLeftValue, $nodeRightValue)
    {
        $tableName = $this->getName();
        $nodeWidth = $nodeRightValue - $nodeLeftValue + 1;
        $sql = <<<SQL
delete from {@table} where `left_value` between :node_left and :node_right;
update {@table} set `right_value` = `right_value` - :node_width where `right_value` > :node_right;
update {@table} set `left_value` = `left_value` - :node_width where `left_value` > :node_right;
SQL;

        $sql = strtr($sql, [
            '{@table}' => $tableName,
        ]);
        $this->getClient()
            ->master()
            ->statement($sql, [
                ':node_left' => $nodeLeftValue,
                ':node_right' => $nodeRightValue,
                ':node_width' => $nodeWidth,
            ]);
    }

}