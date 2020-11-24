<?php

namespace phpframe;

# 定义目录参数
define("ROOT_PATH", __DIR__);

// 加载基础文件
require '../library/base.php';

# 检测admin模块目录，若不存在，创建模块及相应目录
# Build::checkDir('command');

# 指定访问api模块
# Bootstrap::getInstance()->bindModule('api')->run();
Bootstrap::getInstance()->run();