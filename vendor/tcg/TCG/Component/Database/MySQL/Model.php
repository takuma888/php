<?php


namespace TCG\Component\Database\MySQL;

use TCG\Component\Database\MySQL\Query\QueryBuilder;
use TCG\Component\Util\StringUtil;

/**
 * Class Model
 * @package TCG\Component\Database\MySQL
 * @property int $id
 */
abstract class Model implements \ArrayAccess
{
    /**
     * @var array
     */
    private $_fields = [];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Table
     */
    private $_table;

    /**
     * @var array
     */
    private $_modified = [];

    /**
     * Model constructor.
     * @param Table $table
     * @param array $fields
     */
    public function __construct(Table $table, array $fields = [])
    {
        $this->_fields = $table->getFields();
        $this->_table = $table;
        if (!isset($this->_fields['id'])) {
            $this->_fields['id'] = 0;
        }
        foreach ($fields as $field => $value) {
            if (($property = $this->hasProperty($field)) != false) {
                $this->{$property} = $fields[$field];
            }
        }
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @param $key
     * @return string
     */
    public function key2Property($key)
    {
        $property = lcfirst(StringUtil::camelcase($key));
        return $property;
    }

    /**
     * @param $property
     * @return string
     */
    public function property2Key($property)
    {
        $field = StringUtil::underscore($property);
        return $field;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $return = [];
        $rc = new \ReflectionClass($this);
        foreach ($rc->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
            $property = $property->getName();
            $field = $this->property2Key($property);
            $return[$field] = $this->{$property};
        }
        return $return;
    }

    /**
     * @return array
     */
    public function toRawArray()
    {
        $return = [];
        foreach ($this->_fields as $field => $defaultValue) {
            if (($property = $this->hasProperty($field)) != false) {
                $return[$field] = $this->{$property};
            }
        }
        return $return;
    }


    /**
     * @param $field
     * @return bool|string
     */
    private function hasProperty($field)
    {
        $property = $this->key2Property($field);
        if (property_exists($this, $property)) {
            return $property;
        }
        return false;
    }


    /**
     * @return $this|int
     */
    public function insert()
    {
        $table = $this->_table;
        if ($this->id > 0) {
            /** @var Model $update */ // 为了编辑器好看才加的一个错误的注释
            $update = $this->update();
            return $update;
        }
        $lastInsertId = $table->insert($this->toRawArray());
        $this->id = $lastInsertId;
        return $this;
    }

    /**
     * @return int
     */
    public function update()
    {
        $table = $this->_table;
        $data = $this->toRawArray();
        $id = $data['id'];
        return $table->update($this->_modified, function (QueryBuilder $queryBuilder) use ($id) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('id', ':id'))->setParameter(':id', $id);
        }, $data);
    }


    /**
     * @return $this
     */
    public function merge()
    {
        $table = $this->_table;
        $data = $this->toRawArray();
        unset($data['id']);
        $table->merge($data);
        return $this;
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $table = $this->_table;
        $id = $this->id;
        $table->delete(function (QueryBuilder $queryBuilder, Client $client) use ($id) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('id', ':id'))->setParameter(':id', $id);
        });
        return true;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $old_value = $this->get($key);
        $setter = 'set' . StringUtil::camelcase($key);
        if (method_exists($this, $setter)) {
            $this->$setter($value);

        } else {
            if (($property = $this->hasProperty($key)) != false) {
                $this->{$property} = $value;
            }
        }
        // 检查数据变化情况
        if ($old_value != $value) {
            $this->_modified[$key] = $value;
        }
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function get($key)
    {
        $getter = 'get' . StringUtil::camelcase($key);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (($property = $this->hasProperty($key)) != false) {
            return $this->{$property};
        } else {
            throw new \Exception("field {$key} not exists in model field list: [" . implode(', ', $this->getFields()) . "]");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        try {
            return $this->get($offset) != null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        $key = StringUtil::underscore($key);
        return $this->get($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $key = StringUtil::underscore($key);
        $this->set($key, $value);
    }
}