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



}