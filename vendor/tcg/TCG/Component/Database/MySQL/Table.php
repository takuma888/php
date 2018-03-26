<?php

namespace TCG\Component\Database\MySQL;


abstract class Table
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $name;

    /**
     * Table constructor.
     * @param Client $client
     * @param string $name
     */
    public function __construct(Client $client, $name)
    {
        $this->client = $client;
        $this->name = $name;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $partition
     * @return string
     */
    public function getName(array $partition = [])
    {
        $client = $this->getClient();
        return $client->getTableName($this->name, $partition);
    }


    /**
     * @param array $fields
     * @return string
     * @throws \Exception
     */
    public function insert(array $fields)
    {
        $client = $this->getClient();
        $tableName = $this->getName();
        $queryBuilder = $client->createQueryBuilder()->insert($tableName);
        foreach ($fields as $field => $value) {
            if ($value !== null) {
                $queryBuilder->setValue('`' . $field . '`', ':' . $field)->setParameter(':' . $field, $value);
            }
        }
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->master()->statement($sql, $params);
            return $stmt->getLastInsertId();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $multiFields
     * @return string
     * @throws \Exception
     */
    public function multiInsert(array $multiFields)
    {
        $client = $this->getClient();
        $tableName = $this->getName();
        $queryBuilder = $client->createQueryBuilder()->insert($tableName);
        $row_count = 0;
        $multiValues = [];
        foreach ($multiFields as $fields) {
            $row_count += 1;
            $values = [];
            foreach ($fields as $field => $value) {
                $values['`' . $field . '`'] = ':' . $field . '_' . $row_count;
                $queryBuilder->setParameter(':' . $field . '_' . $row_count, $value);
            }
            $multiValues[] = $values;
        }
        $queryBuilder->values($multiValues);

        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->master()->statement($sql, $params);
            return $stmt->getLastInsertId();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $fields
     * @param array $updates
     * @return string
     * @throws \Exception
     */
    public function merge(array $fields, array $updates = [])
    {
        $client = $this->getClient();
        $tableName = $this->getName();
        $queryBuilder = $client->createQueryBuilder()->insert($tableName);
        foreach ($fields as $field => $value) {
            if ($value !== null) {
                $queryBuilder->setValue('`' . $field . '`', ':' . $field)->setParameter(':' . $field, $value);
            }
        }
        $sql = $queryBuilder->getSQL();
        $duplicateUpdates = [];
        foreach ($fields as $field) {
            $duplicateUpdates[$field] = "`{$field}` = VALUES(`{$field}`)";
        }
        foreach ($updates as $field => $expr) {
            $duplicateUpdates[$field] = $expr;
        }
        $sql .= "ON DUPLICATE KEY UPDATE " . implode(', ', $duplicateUpdates);
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->master()->statement($sql, $params);
            return $stmt->getLastInsertId();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $multiFields
     * @param array $updates
     * @return string
     * @throws \Exception
     */
    public function multiMerge(array $multiFields, array $updates = [])
    {
        $client = $this->getClient();
        $tableName = $this->getName();
        $queryBuilder = $client->createQueryBuilder()->insert($tableName);
        $row_count = 0;
        $multiValues = [];
        $all_fields = [];
        foreach ($multiFields as $fields) {
            $row_count += 1;
            $values = [];
            foreach ($fields as $field => $value) {
                $all_fields[$field] = $field;
                $values['`' . $field . '`'] = ':' . $field . '_' . $row_count;
                $queryBuilder->setParameter(':' . $field . '_' . $row_count, $value);
            }
            $multiValues[] = $values;
        }
        $queryBuilder->values($multiValues);

        $sql = $queryBuilder->getSQL();
        $duplicateUpdates = [];
        foreach ($all_fields as $field) {
            $duplicateUpdates[$field] = "`{$field}` = VALUES(`{$field}`)";
        }
        foreach ($updates as $field => $expr) {
            $duplicateUpdates[$field] = $expr;
        }
        $sql .= "ON DUPLICATE KEY UPDATE " . implode(', ', $duplicateUpdates);
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->master()->statement($sql, $params);
            return $stmt->getLastInsertId();
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param \Closure|null $condition
     * @param array $partition
     * @return int
     * @throws \Exception
     */
    public function delete(\Closure $condition = null, array $partition = [])
    {
        $client = $this->getClient();
        $tableName = $this->getName($partition);
        $queryBuilder = $client->createQueryBuilder()->delete($tableName);
        if ($condition) {
            call_user_func_array($condition, [$queryBuilder, $client]);
        }
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->master()->statement($sql, $params);
            return $stmt->getAffectedRowCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $fields
     * @param \Closure|null $condition
     * @return int
     * @throws \Exception
     */
    public function update(array $fields, \Closure $condition = null)
    {
        $client = $this->getClient();
        $tableName = $this->getName($fields);
        $queryBuilder = $client->createQueryBuilder()->update($tableName);
        foreach ($fields as $field => $value) {
            if ($value !== null) {
                $queryBuilder->set($field, ':' . $field)->setParameter(':' . $field, $value);
            }
        }
        if ($condition) {
            call_user_func_array($condition, [$queryBuilder, $client]);
        }
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->master()->statement($sql, $params);
            return $stmt->getAffectedRowCount();
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param \Closure|null $condition
     * @param array $partition
     * @return mixed|null
     * @throws \Exception
     */
    public function one(\Closure $condition = null, array $partition = [])
    {
        $client = $this->getClient();
        $tableName = $this->getName($partition);
        $queryBuilder = $client->createQueryBuilder();
        if ($condition) {
            call_user_func_array($condition, [$queryBuilder, $client]);
        }
        $select = $queryBuilder->getQueryPart('select');
        if (empty($select)) {
            $fields = [];
            foreach ($this->getFields() as $field => $default) {
                $fields[] = '`' . $field . '`';
            }
            $queryBuilder->select(implode(', ', $fields));
        }
        $from = $queryBuilder->getQueryPart('from');
        if (empty($from)) {
            $queryBuilder->from($tableName);
        }
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->slave()->statement($sql, $params);
            $row = $stmt->one();
            if (!$row) {
                return null;
            }
            return $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param \Closure|null $condition
     * @param array $partition
     * @return array
     * @throws \Exception
     */
    public function all(\Closure $condition = null, array $partition = [])
    {
        $client = $this->getClient();
        $tableName = $this->getName($partition);
        $queryBuilder = $client->createQueryBuilder();
        if ($condition) {
            call_user_func_array($condition, [$queryBuilder, $client]);
        }
        $select = $queryBuilder->getQueryPart('select');
        if (empty($select)) {
            $fields = [];
            foreach ($this->getFields() as $field => $default) {
                $fields[] = '`' . $field . '`';
            }
            $queryBuilder->select(implode(', ', $fields));
        }
        $from = $queryBuilder->getQueryPart('from');
        if (empty($from)) {
            $queryBuilder->from($tableName);
        }
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->slave()->statement($sql, $params);
            $all = $stmt->all();
            return $all;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param \Closure|null $condition
     * @param array $partition
     * @return int
     * @throws \Exception
     */
    public function size(\Closure $condition = null, array $partition = [])
    {
        $client = $this->getClient();
        $tableName = $this->getName($partition);
        $queryBuilder = $client->createQueryBuilder();
        if ($condition) {
            call_user_func_array($condition, [$queryBuilder, $client]);
        }
        $queryBuilder->select('COUNT(*) as `num`');
        $from = $queryBuilder->getQueryPart('from');
        if (empty($from)) {
            $queryBuilder->from($tableName);
        }
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->slave()->statement($sql, $params);
            $row = $stmt->one();
            if (!$row) {
                return 0;
            }
            return $row['num'];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $page
     * @param $size
     * @param \Closure|null $condition
     * @param array $partition
     * @return Paginate
     * @throws \Exception
     */
    public function paginate($page, $size, \Closure $condition = null, array $partition = [])
    {
        $size = max(1, $size);
        $client = $this->getClient();
        $tableName = $this->getName($partition);
        $queryBuilder = $client->createQueryBuilder();
        if ($condition) {
            call_user_func_array($condition, [$queryBuilder, $client]);
        }
        $from = $queryBuilder->getQueryPart('from');
        if (empty($from)) {
            $queryBuilder->from($tableName);
        }
        $count = $this->size($condition, $partition);
        $queryBuilder->setFirstResult(max(0, ($page - 1) * $size));
        $queryBuilder->setMaxResults($size);
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        try {
            $stmt = $client->slave()->statement($sql, $params);
            return new Paginate($stmt, $count, $page, $size);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param array $option
     * @return array|string
     */
    public function create(array $option = [])
    {
        $option += [
            'drop' => false,
            'exec' => true,
        ];
        $client = $this->getClient();

        $sql = [];
        $sqlTpl = 'CREATE DATABASE IF NOT EXISTS `{@database}` DEFAULT CHARSET {@charset} COLLATE {@collate};' . "\n";
        foreach ($client->getDbNameRange($this->name) as $dbName) {
            $statement = strtr($sqlTpl, [
                '{@database}' => $dbName,

            ]);
            $sql[] = $statement;
        }
        $dropSqlTpl = '';
        if ($option['drop']) {
            $dropSqlTpl = 'DROP TABLE IF EXISTS {@table};' . "\n";
        }
        $sqlTpl = $dropSqlTpl . $this->getCreateTableSQL();
        foreach ($client->getTableNameRange($this->name) as $dbTableName) {
            $statement = strtr($sqlTpl, [
                '{@table}' => $dbTableName,
                '{@charset}' => $client->getCharset(),
                '{@collate}' => $client->getCollate(),
            ]);
            $sql[] = $statement;
        }
        $sql = implode("\n", $sql);
        if ($option['exec']) {
            $client->master()->statement($sql);
        }
        return $sql;
    }

    /**
     * field => default value
     * @return array
     */
    abstract public function getFields();

    /**
     * @return string
     */
    abstract public function getCreateTableSQL();
}