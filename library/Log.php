<?php

namespace phpframe;

use phpframe\exception\NotFoundException;

class Log
{
    private $handler;

    static private $instance;

    static public function getInstance($driver = 'file')
    {
        if (false == isset(static::$instance[$driver])) {
            $options = Config::getConfig('log', $driver);
            static::$instance[$driver] = new self($driver, $options);
        }
        return static::$instance[$driver];
    }

    private function __construct($driver, array $options = [])
    {
        $driver = ucfirst($driver);
        $class = 'phpframe\driver\log\\' . $driver;
        if (!class_exists($class)) {
            throw new NotFoundException("找不到相应的日志驱动类：" . $class);
        }
        $this->handler = new $class($options);
    }

    /**
     * 写日志
     * @param string $data
     * @return bool|int
     */
    public function write($data = '')
    {
        return $this->handler->write($data);
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}