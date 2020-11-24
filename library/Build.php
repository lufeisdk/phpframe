<?php

namespace phpframe;

class Build
{
    static protected $controller = '<?php
namespace app\[MODULE]\controllers;
use phpframe\Controller;
class [CONTROLLER]Controller extends Controller {
    public function index(){
        echo "Hello Module [MODULE]!";
    }
}';

    static protected $model = '<?php
namespace app\[MODULE]\models;
class [MODEL]Model {

}';

    // 检测应用目录是否需要自动创建
    public static function checkDir($module = '')
    {
        if (!is_dir(APP_PATH . $module)) {
            // 创建模块的目录结构
            self::buildAppDir($module);
        } elseif (!is_dir(RUNTIME_PATH)) {
            // 检查缓存目录
            self::buildRuntime();
        }
    }

    // 创建应用和模块的目录结构
    public static function buildAppDir($module)
    {
        // 没有创建的话自动创建
        if (!is_dir(APP_PATH)) mkdir(APP_PATH, 0755, true);
        if (is_writeable(APP_PATH)) {
            $dirs = array(
                APP_PATH . $module . '/',
                APP_PATH . $module . '/controllers/',
                APP_PATH . $module . '/models/',
                APP_PATH . $module . '/config/',
                APP_PATH . $module . '/views/',
            );
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) mkdir($dir, 0755, true);
            }
            // 写入目录安全文件
            self::buildDirSecure($dirs);

            // 写入配置文件
            self::buildConfig($module);

            // 生成模块的测试控制器
            $controller_list = Config::get('build_controller_list', 'app');
            if ($controller_list) {
                // 自动生成的控制器列表（注意大小写）
                $list = explode(',', $controller_list);
                foreach ($list as $controller) {
                    self::buildController($module, $controller);
                }
            } else {
                // 生成默认的控制器
                self::buildController($module);
            }
            // 生成模块的模型
            $model_list = Config::get('build_model_list', 'app');
            if ($model_list) {
                // 自动生成的控制器列表（注意大小写）
                $list = explode(',', $model_list);
                foreach ($list as $model) {
                    self::buildModel($module, $model);
                }
            }
        } else {
            header('Content-Type:text/html; charset=utf-8');
            exit('应用目录[' . APP_PATH . ']不可写，目录无法自动生成！<BR>请手动生成项目目录~');
        }
    }

    // 创建配置文件
    public static function buildConfig($module)
    {
        $config = Config::all();
        $config_items = array_keys($config);
        foreach ($config_items as $item) {
            // 写入模块配置文件
            $file = APP_PATH . $module . '/config/' . $item . '.php';
            if (!is_file($file)) {
                file_put_contents($file, "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);");
            }
        }
    }

    // 创建控制器类
    public static function buildController($module, $controller = 'Index')
    {
        $file = APP_PATH . $module . '/controllers/' . $controller . 'Controller.php';
        if (!is_file($file)) {
            $content = str_replace(array('[MODULE]', '[CONTROLLER]'), array($module, $controller), self::$controller);

            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($file, $content);
        }
    }

    // 创建模型类
    public static function buildModel($module, $model)
    {
        $file = APP_PATH . $module . '/models/' . $model . 'Model.php';
        if (!is_file($file)) {
            $content = str_replace(array('[MODULE]', '[MODEL]'), array($module, $model), self::$model);

            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($file, $content);
        }
    }

    // 创建缓存目录
    public static function buildRuntime()
    {
        if (!is_dir(RUNTIME_PATH)) {
            mkdir(RUNTIME_PATH);
        } elseif (!is_writeable(RUNTIME_PATH)) {
            header('Content-Type:text/html; charset=utf-8');
            exit('目录 [ ' . RUNTIME_PATH . ' ] 不可写！');
        }

        !defined('CACHE_PATH') && define('CACHE_PATH', RUNTIME_PATH . DS . 'cache');
        !defined('LOG_PATH') && define('LOG_PATH', RUNTIME_PATH . DS . 'log');
        !defined('TEMP_PATH') && define('TEMP_PATH', RUNTIME_PATH . DS . 'temp');
        !defined('DATA_PATH') && define('DATA_PATH', RUNTIME_PATH . DS . 'data');
        if (!is_dir(CACHE_PATH)) mkdir(CACHE_PATH);  // 模板缓存目录
        if (!is_dir(LOG_PATH)) mkdir(LOG_PATH);    // 日志目录
        if (!is_dir(TEMP_PATH)) mkdir(TEMP_PATH);   // 数据缓存目录
        if (!is_dir(DATA_PATH)) mkdir(DATA_PATH);   // 数据文件目录
        return true;
    }

    // 生成安全文件
    public static function buildDirSecure($dirs = [])
    {
        // 自动写入目录安全文件
        $content = ' ';
        $secure_filename = 'index.html';
        foreach ($dirs as $dir) {
            file_put_contents($dir . $secure_filename, $content);
        }
    }
}