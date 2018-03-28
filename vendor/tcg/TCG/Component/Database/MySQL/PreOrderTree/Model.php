<?php

namespace TCG\Component\Database\MySQL\PreOrderTree;

use TCG\Component\Database\MySQL\Model as BaseModel;


/**
 * Class Model
 * @package TCG\Component\Database\MySQL\PreOrderTree
 * @property int $leftValue
 * @property int $rightValue
 * @property int $width
 * @property int $depth
 */
abstract class Model extends BaseModel
{

    protected $leftValue;
    protected $rightValue;
    protected $width;
    protected $depth;

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
    public function getSubTree()
    {
        /** @var Table $table */
        $table = $this->getTable();
        $results = $table->getSubTree($this->id);
        $return = [];
        foreach ($results as $row) {
            $return[] = $table->model($row);
        }
        return $return;
    }

    /**
     * @return $this[]
     */
    public function getTree()
    {
        /** @var Table $table */
        $table = $this->getTable();
        $results = $table->getTree();
        $return = [];
        foreach ($results as $row) {
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
        $table = $this->getTable();
        $row = $table->getDirectParentNode($this->id);
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
        $table = $this->getTable();
        $results = $table->getDirectChildNodes($this->id);
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
        $table = $this->getTable();
        $path = $table->getPath($this->id);
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
        $table = $this->getTable();
        /** @var Model $childId */ // 只是为了编辑器好看增加的注释
        $childId = $table->insertChildNode($fields, $this->rightValue);
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
        $table = $this->getTable();
        $table->removeNode($this->leftValue, $this->rightValue);
    }

    /**
     * 删除，连同子节点一并删除
     */
    public function removeRecursive()
    {
        /** @var Table $table */
        $table = $this->getTable();
        $table->removeRecursiveNodes($this->leftValue, $this->rightValue);
    }

}