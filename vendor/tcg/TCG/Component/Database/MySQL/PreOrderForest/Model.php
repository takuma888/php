<?php

namespace TCG\Component\Database\MySQL\PreOrderForest;

use TCG\Component\Database\MySQL\Model as BaseModel;

/**
 * Class Model
 * @package TCG\Component\Database\MySQL\PreOrderForest
 * @property int $leftValue
 * @property int $rightValue
 * @property int $width
 * @property int $depth
 * @property int $rootValue
 */
abstract class Model extends BaseModel
{
    protected $leftValue;
    protected $rightValue;
    protected $width;
    protected $depth;
    protected $rootValue;

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->leftValue == 0;
    }

    /**
     * @return $this[]
     */
    public function getTree()
    {
        /** @var Table $table */
        $table = $this->table;
        $result = $table->getTree($this->rootValue);
        $return = [];
        foreach ($result as $row) {
            $return[] = $table->model($row);
        }
        return $return;
    }


    /**
     * @return $this[]
     */
    public function getSubTree()
    {
        /** @var Table $table */
        $table = $this->table;
        $result = $table->getSubTree($this->id, $this->rootValue);
        $return = [];
        foreach ($result as $row) {
            $return[] = $table->model($row);
        }
        return $return;
    }

    /**
     * @return null|$this
     */
    public function getDirectParent()
    {
        /** @var Table $table */
        $table = $this->table;
        $row = $table->getDirectParentNode($this->id, $this->rootValue);
        $return = null;
        if ($row) {
            /** @var Model $return */
            $return = $table->model($row);
        }
        return $return;
    }

    /**
     * @return $this[]
     */
    public function getDirectChildren()
    {
        /** @var Table $table */
        $table = $this->table;
        $results = $table->getDirectChildNodes($this->id, $this->rootValue);
        $return = [];
        foreach ($results as $row) {
            $return[] = $table->model($row);
        }
        return $return;
    }

    /**
     * @return $this[]
     */
    public function getPath()
    {
        /** @var Table $table */
        $table = $this->table;
        $path = $table->getPath($this->id, $this->rootValue);
        $return = [];
        foreach ($path as $row) {
            $return[] = $table->model($row);
        }
        return $return;
    }

    /**
     * @param array $fields
     * @param bool $returnNewModel
     * @return string|$this
     */
    public function insertChild(array $fields, $returnNewModel = false)
    {
        /** @var Table $table */
        $table = $this->table;
        /** @var Model $childId */ // 只是为了编辑器好看增加的注释
        $childId = $table->insertChildNode($fields, $this->rightValue, $this->rootValue);
        if ($returnNewModel) {
            $row = $table->getNode($childId);
            /** @var Model $model */
            $model = $table->model($row);
            return $model;
        }
        return $childId;
    }

    /**
     * 删除，保留子节点
     */
    public function remove()
    {
        /** @var Table $table */
        $table = $this->table;
        $table->removeNode($this->leftValue, $this->rightValue, $this->rootValue);
    }

    /**
     * 删除，连同子节点一并删除
     */
    public function removeRecursive()
    {
        /** @var Table $table */
        $table = $this->table;
        $table->removeRecursiveNodes($this->leftValue, $this->rightValue, $this->rootValue);
    }
}