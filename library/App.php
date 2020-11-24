<?php

namespace phpframe;

class App
{
    public static function run($class, $action, $params)
    {
        $ctl = new $class();

        $skipActions = $ctl->_skip_action;

        $bindActions = $ctl->_bind_action;

        # 判断控制器类是否设置了跳过中间件或者设置了指定中间件执行的方法
        if (in_array($action, $skipActions) || (!empty($bindActions) && !in_array($action, $bindActions))) {
            return self::dispatch($ctl, $action, $params);
        }

        self::init($ctl, $action, $params);
    }

    public static function init($class, $action, $params)
    {
        $handler = function () use ($class, $action, $params) {
            return self::dispatch($class, $action, $params);
        };

        $middleware = new Middleware;

        # 加载中间件配置
        if ($middlewares = Config::all(BIND_MODULE, 'middleware')) {
            # 导入中间件
            $middleware->import($middlewares);
        }

        # 运行中间件
        $run = $middleware->run($handler);
        $run(Request::getInstance());

        # 控制器方法执行之后调用
        $class->after();
    }

    // 方法执行调度
    public static function dispatch($class, $action, $params)
    {
        # 控制器初始化执行方法
        $class->init();

        # 控制器方法执行之前调用
        $class->before();

        if (PHP_SAPI == 'cli') {
            # 参数传递
            $class->_params = $params;

            return $class->$action();
        } else {
            return $class->$action(Request::getInstance());
        }
    }
}