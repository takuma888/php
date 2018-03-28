<?php

namespace TCG\Component\Database\MySQL;


class Statement extends \PDOStatement
{
    /**
     * @var Connection
     */
    public $connection;

    /**
     * @var int
     */
    protected $count = 0;

    /**
     * Statement constructor.
     * @param Connection $connection
     */
    protected function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param null $name
     * @return string
     */
    public function getLastInsertId($name = null)
    {
        return $this->connection->lastInsertId($name);
    }

    /**
     * @return int
     */
    public function getAffectedRowCount()
    {
        return $this->rowCount();
    }

    /**
     * @return mixed
     */
    public function one()
    {
        return $this->fetch();
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->fetchAll();
    }

    /**
     * @param $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @param $count
     * @param \Closure $fallback
     */
    public function chunk($count, \Closure $fallback)
    {
        $total = $this->count;
        $page = ceil($total / $count);
        $i = 1;
        while ($i <= $page) {
            $data = [];
            if ($i < $page) {
                $real_count = $count;
            } else {
                $real_count = $total - $i * $count;
            }
            for ($i = 0; $i < $real_count; $i ++) {
                $data[] = $this->one();
            }
            $continue = call_user_func_array($fallback, [$data]);
            if ($continue === false) {
                break;
            }
            $i += 1;
        }
    }
}