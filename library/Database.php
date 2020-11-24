<?php

namespace phpframe;

class Database
{
    private $_handler;

    public static function getInstance($tagName = 'default')
    {
        static $instance;
        if (empty($instance[$tagName])) {
            $config = Config::all(BIND_MODULE, 'database');
            $instance[$tagName] = new self($config[$tagName]);
        }
        return $instance[$tagName];
    }

    private function __construct($config = [])
    {
        $driver = ucfirst($config['type']);
        $class = 'phpframe\driver\db\\' . $driver;
        if (!class_exists($class)) {
            throw new NotFoundException("找不到相应的数据库驱动类：" . $class);
        }
        $this->_handler = new $class($config);
    }

    /**
     * 查询当前数据库的所有数据库表
     * @param string $dbName
     * @return mixed
     */
    public function getTables($dbName = '')
    {
        return $this->_handler->getTables($dbName);
    }

    /**
     * 获取当前数据库表前缀
     */
    public function getPrefix()
    {
        return $this->_handler->getPrefix();
    }

    /**
     * 执行查询
     * @param $sql
     * @param string $func 调用方法，默认fetch，fetchAll，fetchColumn
     * @param bool $rowCount 是否返回记录数
     * @return int
     */
    public function query($sql, $func = 'fetch', $rowCount = false)
    {
        return $this->_handler->query($sql, $func, $rowCount);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->_handler, $method], $args);
    }
}