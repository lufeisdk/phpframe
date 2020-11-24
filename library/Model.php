<?php

namespace phpframe;

class Model
{
    /**
     * 当前数据库连接对象
     * @var Connection
     */
    protected $_connection;

    /**
     * 当前模型对象
     * @var Model
     */
    protected $_model;

    /**
     * 当前数据表名称（不含前缀）
     * @var string
     */
    protected $_name = '';

    /**
     * 当前数据库表名称（含前缀）
     * @var string
     */
    protected $_table = '';

    /**
     * 当前数据表主键
     * @var string|array
     */
    protected $_pk = 'id';

    /**
     * 当前数据表前缀
     * @var string
     */
    protected $_prefix = '';

    /**
     * 当前数据库查询字段
     * @var string
     */
    protected $_field = '*';

    /**
     * 当前数据库查询条件
     * @var string
     */
    protected $_where = '';

    /**
     * 当前数据库查询排序
     * @var string
     */
    protected $_order = '';

    /**
     * 当前数据库查询限制条数
     * @var string
     */
    protected $_limit = '';

    /**
     * 当前数据库查询分组条件
     * @var string
     */
    protected $_group = '';

    /**
     * 当前数据库查询having条件
     * @var string
     */
    protected $_having = '';

    /**
     * 当前数据库操作value值
     * @var string
     */
    protected $_values = '';

    /**
     * 当前数据库查询语句
     * @var string
     */
    protected $_sql = '';

    public function __construct($tagName = 'default')
    {
        if (!$this->_connection) {
            $this->_connection = Database::getInstance($tagName);
        }
        $this->_prefix = $this->_connection->getPrefix();
    }

    /**
     * 得到当前或者指定名称的数据表
     * @access public
     * @param  string $name
     * @return string
     */
    public function getTable($name = '')
    {
        $name = $name ?: $this->_name;

        return $this->_prefix . $name;
    }

    /**
     * 返回当前的数据库表名称
     * @param string $name
     * @return string
     */
    public function table($name = '')
    {
        if ($name) {
            $this->_table = (false !== strpos($name, $this->_prefix) ? '' : $this->_prefix) . $name;
        } else {
            $this->_table = $this->getTable();
        }
        return $this;
    }

    /**
     * 返回当前查询的字段
     * @param string $fields
     * @return $this
     */
    public function field($fields = '')
    {
        if ($fields) {
            if (false === strpos($fields, '`')) {
                $fields = '`' . str_replace(',', '`,`', $fields) . '`';
            }
            $this->_field = $fields;
        }
        return $this;
    }

    /**
     * 返回当前查询的字段
     * @param string $fields
     * @return $this
     */
    public function fieldRaw($fields = '')
    {
        if ($fields) {
            $this->_field = $fields;
        }
        return $this;
    }

    /**
     * 返回当前查询的where条件
     * @param string $where
     * @return $this
     */
    public function where($where = '')
    {
        if ($where) {
            $this->_where = ' WHERE ' . $where;
        }
        return $this;
    }

    public function order($order = '')
    {
        if ($order) {
            $this->_order = ' ORDER BY ' . $order;
        }
        return $this;
    }

    public function group($group = '')
    {
        if ($group) {
            $this->_group = ' GROUP BY ' . $group;
        }
        return $this;
    }

    public function having($having = '')
    {
        if ($having) {
            $this->_having = ' HAVING ' . $having;
        }
        return $this;
    }

    /**
     * 返回当前查询的限制查询记录数
     * @param string $limit
     * @return $this
     */
    public function limit($limit = '')
    {
        if ($limit) {
            $this->_limit = ' LIMIT ' . $limit;
        }
        return $this;
    }

    public function values($values)
    {
        if (is_array($values)) {
            $this->_values = "'" . join("','", $values) . "'";
        } elseif (is_string($values)) {
            $this->_values = $values;
        }
    }

    /**
     * 查询某个字段值
     * @return string
     */
    public function column($field = '')
    {
        if ($field) {
            $this->_field = $field;
        }
        $sql = sprintf("SELECT %s FROM %s %s %s %s %s LIMIT 1", $this->_field, $this->_table, $this->_where, $this->_order, $this->_group, $this->_having);
        $ret = $this->query($sql, 'fetchColumn');
        return $ret;
    }

    /**
     * 单条查询
     * @return string
     */
    public function find($field = '')
    {
        if ($field) {
            $this->_field = $field;
        }
        $sql = sprintf("SELECT %s FROM %s %s %s %s %s LIMIT 1", $this->_field, $this->_table, $this->_where, $this->_order, $this->_group, $this->_having);
        $ret = $this->query($sql, 'fetch');
        return $ret;
    }

    /**
     * 批量查询
     * @return string
     */
    public function select($field = '')
    {
        if ($field) {
            $this->_field = $field;
        }
        $sql = sprintf("SELECT %s FROM %s %s %s %s %s %s", $this->_field, $this->_table, $this->_where, $this->_order, $this->_group, $this->_having, $this->_limit);
        $ret = $this->query($sql);
        return $ret;
    }

    /**
     * 支持单条插入数据
     * @param array $data
     * $param bool $flag 是否返回自增ID
     * @return int 返回自增id
     */
    public function insert($data = array(), $flag = false)
    {
        $this->handleFormat('insert', $data);
        $sql = sprintf("INSERT INTO %s(%s)VALUES(%s)", $this->_table, $this->_field, $this->_values);
        $num = $this->query($sql, 'fetch', true);
        return $flag ? $num : $this->_connection->lastInsertId();
    }

    /**
     * 更新数据
     * @param array $data
     * @return string
     */
    public function update($data = array())
    {
        $this->handleFormat('update', $data);
        $sql = sprintf("UPDATE %s SET %s %s", $this->_table, $this->_values, $this->_where);
        $ret = $this->query($sql, 'fetch', true);
        return $ret;
    }

    /**
     * 删除数据
     * @return string
     */
    public function delete()
    {
        $sql = sprintf("DELETE FROM %s %s", $this->_table, $this->_where);
        $ret = $this->query($sql, 'fetch', true);
        return $ret;
    }

    /**
     * 执行查询
     * @param $sql
     * @param string $func 调用方法，默认fetch，fetchAll，fetchColumn
     * @param bool $rowCount 是否返回记录数
     * @return int
     */
    public function query($sql, $func = 'fetchAll', $rowCount = false)
    {
        $this->_sql = $sql;
        $this->reset();
        return $this->_connection->query($sql, $func, $rowCount);
    }

    /**
     * 返回最后一条SQL查询语句
     * @return string
     */
    public function getLastSQL()
    {
        return $this->_sql;
    }

    /**
     * 根据类型处理字段格式
     * @param $type 类型：insert，update
     * @param array $data
     */
    protected function handleFormat($type, $data = array())
    {
        if (empty($data)) {
            return;
        }

        if ($type == 'insert') {
            $field = join(',', array_keys($data));
            $this->field($field);
            $this->values(array_values($data));
        } elseif ($type == 'update') {
            $str = '';
            foreach ($data as $field => $value) {
                $str .= "`{$field}`='{$value}',";
            }
            $this->values(rtrim($str, ','));
        }
    }

    // 初始化字段
    protected function reset()
    {
        $this->_field = '*';
        $this->_where = '';
        $this->_values = '';
        $this->_order = '';
        $this->_limit = '';
        $this->_group = '';
        $this->_having = '';
    }
}