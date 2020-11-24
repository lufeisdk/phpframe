<?php

namespace phpframe;

use phpframe\exception\NotFoundException;

class Bootstrap
{
    protected $_module;

    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    // 绑定模块
    public function bindModule($module)
    {
        define('BIND_MODULE', $module);
        $this->_module = $module;
        return $this;
    }

    // 执行程序
    public function run()
    {
        $route = Route::getInstance($this->_module);
        $module = $route->getModule();
        $controller = $route->getController() . 'Controller';
        $action = $route->getAction();
        $params = $route->getParams();

        $module_dir = APP_PATH . $module;
        if (!is_dir($module_dir)) {
            throw new NotFoundException("找不到相应的模块：" . BIND_MODULE);
        }

        # 加载模块对应的配置
        Config::all($module);

        $file = $module_dir . DS . 'controllers' . DS . $controller . '.php';
        if (!is_file($file)) {
            throw new NotFoundException("找不到对应的控制器类文件：" . $file);
        }

        include_once $file;
        $className = 'app\\' . $module . '\controllers\\' . $controller;
        if (!method_exists($className, $action)) {
            throw new NotFoundException("找不到类方法：" . $className . '->' . $action);
        }

        App::run($className, $action, $params);
    }
}