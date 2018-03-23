<?php

namespace TCG\Component\Database\MySQL;


class Paginate implements \IteratorAggregate
{

    protected $size = 0;
    protected $page = 1;
    protected $first = 1;
    protected $last = 1;
    protected $count = 0;
    protected $stmt;

    public function __construct(Statement $stmt, $count, $page, $size)
    {
        $this->stmt = $stmt;
        $this->size = $size;
        $this->page = $page;
        $this->count = $count;
        $this->first = 1;
        $this->last = max($this->first, ceil($this->count / $size));
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }


    public function getSize()
    {
        return $this->size;
    }


    public function getCurrent()
    {
        return $this->page;
    }


    public function getLast()
    {
        return $this->last;
    }


    public function setLast($last)
    {
        $this->last = $last;
    }


    public function getFirst()
    {
        return $this->first;
    }


    public function setFirst($first)
    {
        $this->first = $first;
    }

    /**
     * @return Statement
     */
    public function getIterator()
    {
        return $this->stmt;
    }
}