<?php

namespace phpframe;

class Route
{
    public $_module;        # 当前模块
    public $_controller;    # 当前调用的控制器类
    public $_action;        # 当前调用的控制器方法
    public $_params = [];   # 当前方法携带的参数

    public static function getInstance($module = '')
    {
        static $instance;
        if (!$instance) {
            $instance = new self($module);
        }
        return $instance;
    }

    private function __construct($module)
    {
        if (PHP_SAPI == 'cli') {
            $argv = $_SERVER['argv'];
            $this->_module = $module;
            $this->_controller = $argv[1] ?? Config::get('default_controller');
            $this->_action = $argv[2] ?? Config::get('default_action');
            $params = count($argv) > 3 ? array_slice($argv, 3, count($argv) - 3) : [];
            $this->_params = $this->getOption($params);
        } else {
            $url = $_SERVER['REQUEST_URI'];
            if (false !== strpos($url, '?')) {
                $url = strstr($url, '?', true); # 截取？之前的uri地址
            }
            if (false !== strpos($url, 'index.php')) {
                $url = strstr($url, 'index.php');   # 截取index.php之后的URI地址
            }
            $argv = $url ? explode('/', $url) : [];
            if (count($argv) < 3) {
                array_push($argv, '');
            }
            list($a, $c, $m) = array_reverse($argv);
            $this->_module = $module ?: ($m ? $m : Config::get('default_module', 'app'));
            $this->_controller = $c ? $c : Config::get('default_controller', 'app');
            $this->_action = $a ? $a : Config::get('default_action', 'app');
        }
        //pp($argv);
    }

    // 获取当前模块
    public function getCurrentModule()
    {
        !defined('BIND_MODULE') && define('BIND_MODULE', $this->_module);
        return $this->_module;
    }

    // 获取当前模块
    public function getModule()
    {
        !defined('BIND_MODULE') && define('BIND_MODULE', $this->_module);
        return $this->_module;
    }

    // 获取当前控制器
    public function getController()
    {
        !defined('BIND_CONTROLLER') && define('BIND_CONTROLLER', $this->_controller);
        return ucfirst($this->_controller);
    }

    // 获取当前方法
    public function getAction()
    {
        !defined('BIND_ACTION') && define('BIND_ACTION', $this->_action);
        return $this->_action;
    }

    // 获取当前调用参数
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 命令行参数解析函数
     * 命令行参数输入支持三种键值对格式：[=:.]
     * 参考形式：id=1 name:ray gender.man
     * 解析之后形成键值对数组
     * 同时也支持无键名形式参数
     * @return array
     */
    protected function getOption($params)
    {
        $data = [];
        if ($params) {
            foreach ($params as $param) {
                if (preg_match('/[=:\.]/', $param, $matches)) {
                    list($pk, $pv) = explode($matches[0], $param);
                    $data[$pk] = $pv;
                } else {
                    $data[] = $param;
                }
            }
        }
        return $data;
    }

    // 防止 clone 多个实例
    private function __clone()
    {
    }

    // 防止反序列化
    private function __wakeup()
    {
    }
}