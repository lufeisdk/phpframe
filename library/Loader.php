<?php

namespace phpframe;

use phpframe\exception\NotFoundException;

class Loader
{
    /**
     * 类库别名
     * @var array
     */
    protected static $classAlias = [];

    // 注册自动加载机制
    public static function register()
    {
        # 注册系统自动加载
        spl_autoload_register('phpframe\\Loader::autoload', true, true);

        # 加载公共模块
        self::loadCommon();
    }

    // 自动加载
    public static function autoload($class)
    {
        if (isset(self::$classAlias[$class])) {
            return class_alias(self::$classAlias[$class], $class);
        }

        if ($file = self::findFile($class)) {
            include $file;
            return true;
        }
        throw new NotFoundException("找不到相应的类文件：" . $class);
    }

    // 加载公共模块
    private static function loadCommon()
    {
        # 加载公共配置
        Config::all();

        # 加载公共函数
        include APP_PATH . 'common' . DS . 'functions.php';

        # 加载公共trait
        foreach (glob(APP_PATH . 'common' . DS . 'traits/*.php') as $file) {
            include $file;
        }
        return true;
    }

    /**
     * 查找文件
     * @access private
     * @param  string $class
     * @return string|false
     */
    private static function findFile($class)
    {
        # 引入Twig类
        if (false !== strpos($class, 'Twig')) {
            $class = 'phpframe/driver/template/' . $class;
        }

        # 引入Extend扩展类
        if (false !== strpos($class, 'util')) {
            $class = str_replace('util', 'extend', $class);
        }

        $class = str_replace('\\', '/', $class);
        $class = str_replace('phpframe', 'library', $class);
        $file = PROJECT_PATH . DS . $class . '.php';

        return is_file($file) ? $file : false;
    }

    // 注册类别名
    public static function addClassAlias($alias, $class = null)
    {
        if (is_array($alias)) {
            self::$classAlias = array_merge(self::$classAlias, $alias);
        } else {
            self::$classAlias[$alias] = $class;
        }
    }
}