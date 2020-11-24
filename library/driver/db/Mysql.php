<?php

namespace phpframe\driver\db;

use PDO;
use phpframe\Component;

class Mysql extends Component
{
    # 驱动句柄
    protected $_handler = null;

    # 默认配置
    private static $_config = [
        'host' => '127.0.0.1',
        'user' => 'root',
        'passwd' => 'root',
        'dbname' => '',
        'port' => 3306,
        'prefix' => '',
        'charset' => 'utf8mb4',
    ];

    # 服务器断线标识字符
    protected $breakMatchStr = [
        'server has gone away',
        'no connection to the server',
        'Lost connection',
        'is dead or not enabled',
        'Error while sending',
        'decryption failed or bad record mac',
        'server closed the connection unexpectedly',
        'SSL connection has been closed unexpectedly',
        'Error writing data to the connection',
        'Resource deadlock avoided',
        'failed with errno',
    ];

    public function __construct(Array $config = [])
    {
        self::$_options = array_merge(self::$_config, $config);

        $pdostr = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';port=' . $this->port;

        try {
            $this->_handler = new PDO($pdostr, $this->user, $this->passwd);
            $this->_handler->exec("SET names " . $this->charset);
            $this->_handler->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            if ($this->isBreak($e)) {
                return $this->retry();
            }

            throw new \PDOException($e, self::$_options, 'MySQL PDO数据库连接失败~');
        } catch (\Throwable $e) {
            if ($this->isBreak($e)) {
                return $this->retry();
            }

            throw $e;
        } catch (\Exception $e) {
            if ($this->isBreak($e)) {
                return $this->retry();
            }

            throw $e;
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
        $sql = !empty($dbName) ? 'SHOW TABLES FROM ' . $dbName : 'SHOW TABLES ';
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

    /**
     * 关闭数据库（或者重新连接）
     * @access public
     * @return $this
     */
    public function retry()
    {
        $this->_handler = null;

        return new self(self::$_options);
    }

    /**
     * 是否断线
     * @access protected
     * @param  \PDOException|\Exception $e 异常对象
     * @return bool
     */
    protected function isBreak($e)
    {
        $error = $e->getMessage();

        foreach ($this->breakMatchStr as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->_handler, $method], $args);
    }
}