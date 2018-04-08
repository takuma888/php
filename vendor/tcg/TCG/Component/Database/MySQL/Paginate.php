<?php

namespace TCG\Component\Database\MySQL;


class Paginate implements \IteratorAggregate
{

    protected $size = 0;
    protected $page = 1;
    protected $first = 1;
    protected $last = 1;
    protected $count = 0;
    /**
     * @var array
     */
    protected $data = [];

    public function __construct(array $data, $count, $page, $size)
    {
        $this->data = $data;
        $this->size = $size;
        $this->page = $page;
        $this->count = $count;
        $this->first = 1;
        $this->last = max($this->first, ceil($this->count / $size));
    }


    public function getData()
    {
        return $this->data;
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

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }


    public function getPages()
    {
        // 最多显示7页
        $pages = [];
        $startPage = $this->page - 2;
        $startPage = max($this->first, $startPage);
        $endPage = $this->page + 2;
        $endPage = min($this->last, $endPage);
        foreach (range($startPage, $endPage) as $p) {
            $pages[$p] = $p;
        }
        if ($startPage != $this->first) {
            array_unshift($pages, 0);
        }
        if ($endPage != $this->last) {
            array_push($pages, 0);
        }
        return $pages;

    }
}