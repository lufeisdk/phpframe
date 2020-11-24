<?php

namespace phpframe\driver\db;

use PDO;
use phpframe\Component;

class Sqlsrv extends Component
{
    # 驱动句柄
    protected $_handler = null;

    # 默认配置
    private static $_config = [
        'host' => '127.0.0.1',
        'user' => 'root',
        'passwd' => 'root',
        'dbname' => '',
        'prefix' => '',
        'charset' => 'utf8mb4',
    ];

    public function __construct(Array $config = [])
    {
        self::$_options = array_merge(self::$_config, $config);

        $dsn = 'sqlsrv:Database=' . $this->dbname . ';Server=' . $this->host;

        if (!empty($this->port)) {
            $dsn .= ',' . $this->port;
        }

        try {
            $this->_handler = new PDO($dsn, $this->user, $this->passwd);
            $this->_handler->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \PDOException($e, self::$_options, 'SQL Server PDO数据库连接失败~');
        }
    }

    /**
     * 取得数据库的表信息
     * @access public
     * @param  string $dbName
     * @return array
     */
    public function getTables($dbName = '')
    {
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_TYPE = 'BASE TABLE'";
        $pdo = $this->_handler->query($sql);
        $result = $pdo->fetchAll();
        $info = [];

        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }

        return $info;
    }

    /**
     * 获取当前数据库表前缀
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
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
        try {
            $stmt = $this->_handler->prepare($sql);

            $stmt->execute();

            return $rowCount ? $stmt->rowCount() : $stmt->$func();
        } catch (\PDOException $e) {
            throw new PDOException($e, self::$_options, $sql);
        }
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->_handler, $method], $args);
    }
}