<?php

namespace app\command\controllers;

use phpframe\Build;
use phpframe\Controller;

class IndexController extends Controller
{
    public function __construct()
    {
        //$this->_skip_action = ['index'];

        //$this->_bind_action = ['user'];
    }

    /**
     * 测试
     * php cli.php index index a.name age=18 gender:man
     */
    public function index()
    {
        pp($this->_params);
        echo "Hello, this is command module !";
    }

    /**
     * 根据命令行参数创建模块，控制器，模型
     * 命令行执行：
     * php cli.php index build module.api   创建api模块
     * php cli.php index build module.api controller.user 在api模块下创建UserController.php控制器
     * php cli.php index build module.api model.user 在api模块下创建UserModel.php模型
     * php cli.php index build controller.user 在默认模块下创建控制器
     * php cli.php index build model.user 在默认模块下创建模型
     */
    public function build()
    {
        if (empty($this->_params)) {
            die('--END--');
        }

        $module = $this->_params['module'] ?? BIND_MODULE;

        if (isset($this->_params['controller'])) {
            $controller = ucfirst($this->_params['controller']);
            Build::buildController($module, $controller);
            echo $controller . 'Controller.php创建成功~' . PHP_EOL;
        }

        if (isset($this->_params['model'])) {
            $model = ucfirst($this->_params['model']);
            Build::buildModel($module, $model);
            echo $model . 'Model.php创建成功~' . PHP_EOL;
        }

        if (!is_dir(APP_PATH . $module)) {
            Build::buildAppDir($module);
            echo $module . '模块创建成功~' . PHP_EOL;
        }
        echo '--END--';
    }
}