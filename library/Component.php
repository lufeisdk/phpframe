<?php

namespace phpframe;

class Component
{
    static public $_options = [];

    public function __get($name)
    {
        return self::$_options[$name];
    }

    public function __set($name, $value)
    {
        if (isset(self::$_options[$name])) {
            self::$_options[$name] = $value;
        }
    }

    // 当对不可访问属性调用 isset() 或 empty() 时，__isset() 会被调用。
    public function __isset($name)
    {
        return isset(self::$_options[$name]);
    }

    public function __unset($name)
    {
        unset (self::$_options[$name]);
    }
}