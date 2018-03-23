<?php


namespace TCG\Component\Database\MySQL;


class Connection extends \PDO
{
    const MASTER_CONNECTION = 1;
    const SLAVE_CONNECTION = 2;

    protected $connectionKey;

    /**
     * 链接对象
     * @var array|Connection[]
     */
    protected static $connection = [];

    /**
     * 解析dsn字符串
     * @param $dsn
     * @return array
     */
    public static function parseDsn($dsn)
    {
        $dsn = parse_url($dsn);
        $config = array();
        $config['driver'] = strtolower($dsn['scheme']);
        $config['host'] = $dsn['host'];
        if (!empty($dsn['port'])) {
            $config['port'] = $dsn['port'];
        } else {
            $config['port'] = 3306;
        }
        $config['user'] = $dsn['user'];
        $config['pass'] = $dsn['pass'];
        $config['charset'] = 'utf8';
        if (isset($dsn['query'])) {
            $option = array();
            parse_str($dsn['query'], $option);
            $config = array_merge($config, $option);
        }
        return $config;
    }

    /**
     * 链接数据库
     * @param array $config
     * @return Connection
     */
    public static function connect(array $config)
    {
        $key = md5(json_encode($config));
        if (isset(self::$connection[$key])) {
            try {
                self::$connection[$key]->ping();
            } catch (\Exception $e) {
                unset(self::$connection[$key]);
            }
        }
        if (!isset(self::$connection[$key])) {
            $dsn = $config['driver'] . ':host=' . $config['host'] . ';port=' . $config['port'];
            $user = $config['user'];
            $pass = $config['pass'];
            $connectionOptions = array(
//            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
//            \PDO::ATTR_EMULATE_PREPARES => true,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            );
            $pdo = new Connection($dsn, $user, $pass, $connectionOptions);
            $pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, 'TCG\Component\Database\MySQL\Statement');
            $pdo->exec("set @@sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
            if (!empty($config['collation'])) {
                $pdo->exec('SET NAMES ' . $config['charset'] . ' COLLATE ' . $config['collation']);
            } else {
                $pdo->exec('SET NAMES ' . $config['charset']);
            }
            $pdo->setConnectionKey($key);
            self::$connection[$key] = $pdo;
        }
        return self::$connection[$key];
    }


    public function close()
    {
        unset(self::$connection[$this->getConnectionKey()]);
    }


    public function setConnectionKey($connection_index)
    {
        $this->connectionKey = $connection_index;
    }


    public function getConnectionKey()
    {
        return $this->connectionKey;
    }

    public function ping()
    {
        try {
            $this->getAttribute(\PDO::ATTR_SERVER_INFO);
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
                throw $e;
            }
        }
    }


    /**
     * 开启事务
     * @param \Closure $fallback
     */
    public function transaction(\Closure $fallback = null)
    {
        if (!$this->inTransaction()) {
            $this->beginTransaction();
        }
        if ($fallback) {
            try {
                call_user_func($fallback);
                $this->commit();
            } catch (\Exception $e) {
                $this->rollback();
            }
        }
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        if ($this->inTransaction()) {
            parent::commit();
        }
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        if ($this->inTransaction()) {
            parent::rollBack();
        }
    }


    /**
     * @param $sql
     * @param array $params
     * @return Statement
     */
    public function statement($sql, array $params = [])
    {
        try {
            /** @var Statement $statement */
            $statement = $this->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}