<?php

namespace phpframe;

use phpframe\exception\NotFoundException;

class Cache
{
    private $_handler;

    public static function getInstance($driver = 'file')
    {
        static $instance; # 单例实例句柄
        if (empty($instance[$driver])) {
            $options = Config::all(BIND_MODULE, 'cache');
            $instance[$driver] = new self($driver, $options[$driver]);
        }
        return $instance[$driver];
    }

    private function __construct($driver, array $options = [])
    {
        $driver = ucfirst($driver);
        $class = 'phpframe\driver\cache\\' . $driver;
        if (!class_exists($class)) {
            throw new NotFoundException("找不到相应的缓存驱动类：" . $class);
        }
        $this->_handler = new $class($options);
    }

    public function has($name)
    {
        return $this->_handler->has($name);
    }

    public function get($name)
    {
        return $this->_handler->get($name);
    }

    public function set($name, $value, $expire_time = null)
    {
        return $this->_handler->set($name, $value, $expire_time);
    }

    public function inc($name, $step = 1)
    {
        return $this->_handler->inc($name, $step);
    }

    public function dec($name, $step = 1)
    {
        return $this->_handler->dec($name, $step);
    }

    public function rm($name)
    {
        return $this->_handler->rm($name);
    }

    public function clear()
    {
        return $this->_handler->clear();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->_handler, $method], $args);
    }
}