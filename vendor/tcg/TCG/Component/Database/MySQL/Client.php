<?php

namespace TCG\Component\Database\MySQL;


use Psr\Log\LoggerInterface;

class Client
{

    /**
     * 主链接的配置
     * @var array
     */
    protected $masterConfiguration = [];

    /**
     * 从链接的配置
     * @var array
     */
    protected $slavesConfiguration = [];

    /**
     * 数据库分片配置
     * @var array
     */
    protected $shards = [];

    /**
     * 表前缀
     * @var string
     */
    protected $prefix = '';

    /**
     * @var string
     */
    protected $charset = '';

    /**
     * @var string
     */
    protected $collate = '';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(array $config)
    {
        $this->masterConfiguration = $config['master'];
        $this->slavesConfiguration = isset($config['slaves']) ? $config['slaves'] : [];
        $shards = isset($config['shards']) ? $config['shards'] : [];
        foreach ($shards as $shard) {
            $this->shards[$shard['table_name']] = $shard;
        }
        $this->prefix = isset($config['table_prefix']) ? $config['table_prefix'] : '';
        $this->charset = isset($config['charset']) ? $config['charset'] : 'utf8mb4';
        $this->collate = isset($config['collate']) ? $config['collate'] : 'utf8mb4_general_ci';
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return mixed|string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @return mixed|string
     */
    public function getCollate()
    {
        return $this->collate;
    }


    /**
     * 获取主链接配置
     * @return array
     */
    public function getMasterDsnConfig()
    {
        return $this->masterConfiguration;
    }

    /**
     * 获取单个从链接配置
     * @return array
     */
    public function getSlaveDsnConfig()
    {
        if (empty($this->slavesConfiguration)) {
            return $this->masterConfiguration;
        } else {
            $idx = mt_rand(0, count($this->slavesConfiguration) - 1);
            return $this->slavesConfiguration[$idx];
        }
    }


    /**
     * 获取分片配置的数据表
     * @return array
     */
    public function getShardTables()
    {
        return array_keys($this->shards);
    }

    /**
     * 获取所有从链接配置
     * @return array
     */
    public function getSlavesDsnConfig()
    {
        return $this->slavesConfiguration;
    }

    /**
     * 获取指定表的主数据库链接对象
     * @return Connection
     */
    public function getMasterConnection()
    {
        $connection = Connection::connect($this->masterConfiguration, $this);
        return $connection;
    }

    /**
     * @return Connection
     */
    public function master()
    {
        return $this->getMasterConnection();
    }

    /**
     * @return Connection
     */
    public function slave()
    {
        return $this->getSlaveConnection();
    }

    /**
     * @param \Closure|null $fallback
     */
    public function transaction(\Closure $fallback = null)
    {
        $this->master()->transaction($fallback);
    }

    /**
     * 获取指定表的从数据库链接对象
     * @return Connection
     */
    public function getSlaveConnection()
    {
        if (!empty($this->slavesConfiguration)) {
            $index = mt_rand(0, count($this->slavesConfiguration) - 1);
            $slaveDsn = $this->slavesConfiguration[$index];
            $connection = Connection::connect($slaveDsn, $this);
            return $connection;
        } else {
            return $this->getMasterConnection();
        }
    }

    /**
     * 获取指定表的数据库链接对象
     * @param bool $master
     * @return Connection
     */
    public function getConnection($master = true)
    {
        if ($master) {
            return $this->getMasterConnection();
        } else {
            return $this->getSlaveConnection();
        }
    }

    /**
     * @param bool $master
     * @return Connection
     */
    public function connection($master = true)
    {
        return $this->getConnection($master);
    }

    /**
     * @return Query\QueryBuilder
     */
    public function createQueryBuilder()
    {
        return new Query\QueryBuilder($this);
    }

    /**
     * @return Query\Expression\ExpressionBuilder
     */
    public function createExpressionBuilder()
    {
        return new Query\Expression\ExpressionBuilder();
    }


    /**
     * 获取表的名字范围
     * @param $tableName
     * @return array
     */
    public function getTableNameRange($tableName)
    {
        $return = array();
        $tableNames = array();
        // 判断对表的分片
        if (empty($this->shards) || !isset($this->shards[$tableName])) {
            $tableNames[] = $tableName;
        } else {
            $shards = $this->shards[$tableName];
            $maxTableNum = 1;
            if (isset($shards['max_table_num']) && $shards['max_table_num'] > 1) {
                $maxTableNum = intval($shards['max_table_num']);
            }
            $deploy = array();
            if (isset($shards['deploy'])) {
                $deploy = $shards['deploy'];
            }
            if ($maxTableNum > 1) {
                if (in_array(2, $deploy) || in_array(3, $deploy)) {
                    $range = range(1, $maxTableNum);
                    foreach ($range as $idx) {
                        $tableNames[] = $tableName . '_' . $idx;
                    }
                }
            } else {
                $realTableName = $tableName;
                if (in_array(4, $deploy)) {
                    if (!isset($partition['timestamp'])) {
                        $partition['timestamp'] = time();
                    }
                    $realTableName = $tableName . '_' . date('Y_m_d', $partition['timestamp']);
                } elseif (in_array(5, $deploy)) {
                    if (!isset($partition['timestamp'])) {
                        $partition['timestamp'] = time();
                    }
                    $realTableName = $tableName . '_' . date('Y_W', $partition['timestamp']);
                } elseif (in_array(6, $deploy)) {
                    if (!isset($partition['timestamp'])) {
                        $partition['timestamp'] = time();
                    }
                    $realTableName = $tableName . '_' . date('Y_m', $partition['timestamp']);
                }
                if ($this->prefix) {
                    $realTableName = $this->prefix . $realTableName;
                }
                $tableNames[] = $realTableName;
            }
        }

        $dbNames = $this->getDbNameRange($tableName);
        foreach ($dbNames as $dbName) {
            foreach ($tableNames as $tableName) {
                $return[] = '`' . $dbName . '`.`' . $tableName . '`';
            }
        }
        return $return;
    }

    /**
     * 根据指定的表名及分区配置获得真正的表名
     * @param $tableName
     * @param array $partition
     * @return string
     */
    public function getTableName($tableName, array $partition = [])
    {
        // 判断对表的分片
        if (empty($this->shards) || !isset($this->shards[$tableName])) {
            // do nothing
            $realTableName = $tableName;
        } else {
            $realTableName = $tableName;
            $shards = $this->shards[$tableName];
            $maxDbNum = 1;
            $maxTableNum = 1;
            if (isset($shards['max_table_num']) && $shards['max_table_num'] > 1) {
                $maxTableNum = intval($shards['max_table_num']);
            }
            if (isset($shards['max_db_num']) && $shards['max_db_num'] > 1) {
                $maxDbNum = intval($shards['max_db_num']);
            }
            $deploy = array();
            if (isset($shards['deploy'])) {
                $deploy = $shards['deploy'];
            }
            if ($maxTableNum > 1) {
                if (in_array(2, $deploy) || in_array(3, $deploy)) {
                    // 只分表或者分库分表
                    $partitionValue = false;
                    if (isset($shards['table_partition_field'])) {
                        $partitionField = $shards['table_partition_field'];
                        if (isset($partition[$partitionField])) {
                            $partitionValue = $partition[$partitionField];
                        }
                    }
                    if ($partitionValue !== false) {
                        $tmp = false;
                        if (is_int($partitionValue)) {
                            $tmp = $partitionValue;
                        } elseif (!empty($partitionValue)) {
                            $tmp = abs(crc32(strval($partitionValue)));
                        }
                        if ($tmp !== false) {
                            $tableIndex = ($tmp / $maxDbNum) % $maxTableNum + 1;
                            $realTableName = $tableName . '_' . $tableIndex;
                        }
                    }
                }
            }
            if (in_array(4, $deploy)) {
                if (!isset($partition['timestamp'])) {
                    $partition['timestamp'] = time();
                }
                $realTableName = $tableName . '_' . date('Y_m_d', $partition['timestamp']);
            } elseif (in_array(5, $deploy)) {
                if (!isset($partition['timestamp'])) {
                    $partition['timestamp'] = time();
                }
                $realTableName = $tableName . '_' . date('Y_W', $partition['timestamp']);
            } elseif (in_array(6, $deploy)) {
                if (!isset($partition['timestamp'])) {
                    $partition['timestamp'] = time();
                }
                $realTableName = $tableName . '_' . date('Y_m', $partition['timestamp']);
            }
            if ($this->prefix) {
                $realTableName = $this->prefix . $realTableName;
            }
        }
        $dbName = $this->getDbName($tableName, $partition);
        return '`' . $dbName . '`.`' . $realTableName . '`';
    }

    /**
     * 获取表所在的库的名字范围
     * @param $tableName
     * @return array
     * @throws \Exception
     */
    public function getDbNameRange($tableName)
    {
        $return = array();
        if (!isset($this->shards[$tableName]) || !isset($this->shards[$tableName]['db_name'])) {
            throw new \Exception("表 $tableName 分片配置不正确");
        }
        $dbName = $this->shards[$tableName]['db_name'];
        $maxDbNum = 1;
        $shards = $this->shards[$tableName];
        if (isset($shards['max_db_num']) && $shards['max_db_num'] > 1) {
            $maxDbNum = intval($shards['max_db_num']);
        }
        if ($maxDbNum > 1) {
            $deploy = array();
            if (isset($shards['deploy'])) {
                $deploy = $shards['deploy'];
            }
            if (in_array(1, $deploy) || in_array(3, $deploy)) {
                $range = range(1, $maxDbNum);
                foreach ($range as $idx) {
                    $return[] = $dbName . '_' . $idx;
                }
            }
        } else {
            $return[] = $dbName;
        }
        return $return;
    }

    /**
     * 获取表所在的库的名字
     * @param $tableName
     * @param array $partition
     * @return string
     * @throws \Exception
     */
    public function getDbName($tableName, array $partition = [])
    {
        // 判断对库的分片
        if (!isset($this->shards[$tableName]) || !isset($this->shards[$tableName]['db_name'])) {
            throw new \Exception("表 $tableName 配置不正确");
        }
        $dbName = $this->shards[$tableName]['db_name'];
        $maxDbNum = 1;
        $maxTableNum = 1;
        $shards = $this->shards[$tableName];
        if (isset($shards['max_table_num']) && $shards['max_table_num'] > 1) {
            $maxTableNum = intval($shards['max_table_num']);
        }
        if (isset($shards['max_db_num']) && $shards['max_db_num'] > 1) {
            $maxDbNum = intval($shards['max_db_num']);
        }
        if ($maxDbNum > 1) {
            $deploy = array();
            if (isset($shards['deploy'])) {
                $deploy = $shards['deploy'];
            }
            if (in_array(1, $deploy) || in_array(3, $deploy)) {
                // 只分库或者分库分表
                $partitionValue = false;
                if (isset($shards['db_partition_field'])) {
                    $partitionField = $shards['db_partition_field'];
                    if (isset($partition[$partitionField])) {
                        $partitionValue = $partition[$partitionField];
                    }
                }
                if ($partitionValue !== false) {
                    $tmp = false;
                    if (is_int($partitionValue)) {
                        $tmp = $partitionValue;
                    } elseif (!empty($partitionValue)) {
                        $tmp = abs(crc32(strval($partitionValue)));
                    }
                    if ($tmp !== false) {
                        $dbIndex = intval($tmp / $maxTableNum) % $maxDbNum + 1;
                        $dbName = $dbName . '_' . $dbIndex;
                    }
                }
            }
        }
        return $dbName;
    }


    /**
     * @param $tableName
     * @return string
     * @throws \Exception
     */
    public function getModelClass($tableName)
    {
        if (empty($this->shards) || !isset($this->shards[$tableName]) || !isset($this->shards[$tableName]['model'])) {
            throw new \Exception("表 $tableName 配置不正确");
        }
        $tableModelClass = $this->shards[$tableName]['model'];
        return $tableModelClass;
    }
}