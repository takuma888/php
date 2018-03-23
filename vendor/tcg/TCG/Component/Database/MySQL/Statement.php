<?php

namespace TCG\Component\Database\MySQL;


class Statement extends \PDOStatement
{
    /**
     * @var Connection
     */
    public $connection;

    protected function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function getLastInsertId($name = null)
    {
        return $this->connection->lastInsertId($name);
    }


    public function getAffectedRowCount()
    {
        return $this->rowCount();
    }


    public function one()
    {
        return $this->fetch();
    }


    public function all()
    {
        return $this->fetchAll();
    }
}