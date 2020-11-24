<?php

namespace phpframe;

# 定义目录参数
define("ROOT_PATH", __DIR__);                          # 根目录

// 加载基础文件
require '../library/base.php';

# 指定访问api模块
Bootstrap::getInstance()->bindModule('command')->run();