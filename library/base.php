<?php

namespace phpframe;

# 设置时区
ini_set("date.timezone", "PRC");

# 定义反斜杠
define('DS', '/');
define("PROJECT_PATH", dirname(ROOT_PATH));       # 项目目录
define("APP_PATH", PROJECT_PATH . DS . 'app' . DS);    # 应用目录
define("LIB_PATH", PROJECT_PATH . DS . 'library');     # 类库目录
define("RUNTIME_PATH", PROJECT_PATH . DS . 'runtime'); # 运行时目录

require 'Loader.php';

// 注册自动加载
Loader::register();

// 注册错误和异常处理机制
Error::register();

// 注册类库别名
Loader::addClassAlias([
    'Error' => Error::class,
    'Config' => Config::class,
    'Middleware' => Middleware::class,
]);