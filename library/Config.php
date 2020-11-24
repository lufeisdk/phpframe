<?php

namespace phpframe;

use phpframe\exception\NotFoundException;

class Config
{
    private static $_config = [];

    /**
     * 检测配置是否存在
     * @access public static
     * @param  string $name 配置参数名（支持多级配置 .号分割）
     * @param string $filename 配置文件名
     * @param string $module 模块
     * @return bool
     */
    public static function has($name, $filename, $module)
    {
        return !is_null(self::get($name, $filename, $module));
    }

    /**
     * 获取配置
     * @param $name             配置参数名
     * @param string $filename 配置文件名
     * @param string $module 模块
     * @return mixed
     */
    public static function get($name, $filename = 'config', $module = 'common')
    {
        if (!empty(self::$_config[$module][$filename][$name])) {
            return self::$_config[$module][$filename][$name];
        }

        $dir = APP_PATH . $module . DS . 'config' . DS;
        $file = $dir . $filename . '.php';
        if ($module == 'common') {
            $file = $dir . 'config.php';
        }
        if (!is_file($file)) {
            throw new NotFoundException("找不到对应的配置文件：" . $file);
        }

        $config = include $file;
        if ($module != 'common') {
            self::$_config[$module][$filename] = $config;
        } else {
            self::$_config[$module] = $config;
        }

        return self::$_config[$module][$filename][$name];
    }

    public static function set($name, $value)
    {

    }

    /**
     * 根据模块获取当前文件的所有配置信息
     * @param string $filename
     * @param string $module
     * @param string $type 配置类型：log，database等
     * @return mixed
     */
    public static function all($module = 'common', $type = '')
    {
        if (!empty(self::$_config[$module])) {
            if ($type) {
                return self::$_config[$module][$type];
            }
            return self::$_config[$module];
        }

        if ($module != 'common') {
            $files = glob(APP_PATH . "{common,$module}/config/*.php", GLOB_BRACE);
        } else {
            $files = glob(APP_PATH . "common/config/*.php");
        }

        $config = [];
        foreach ($files as $file) {
            if (is_file($file)) {
                $configs = include $file;
                $filename = pathinfo($file)['filename'];
                if ($filename != 'config') {
                    $config[$filename] = isset($config[$filename]) ?
                        array_merge($config[$filename], $configs) :
                        $configs;
                } else {
                    $config = $configs;
                }
            }
        }
        self::$_config[$module] = $config;
        return $type ? $config[$type] : $config;
    }

    /**
     * 根据驱动获取配置
     * @param $type
     * @param $driver
     * @return mixed
     */
    public static function getConfig($type, $driver)
    {
        $module = Route::getInstance()->getCurrentModule();
        $config = self::all($module, $type);
        return $config[$driver];
    }
}