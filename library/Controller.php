<?php

namespace phpframe;

class Controller
{
    public $_params;             # 参数数组

    public $_skip_action = [];   # 跳过中间件执行的方法数组

    public $_bind_action = [];   # 绑定中间件执行的方法数组

    /**
     * 控制器类初始化执行方法
     */
    public function init()
    {
        //echo '--init--' . PHP_EOL;
    }

    /**
     * 控制器执行之前调用方法
     */
    public function before()
    {
        //echo '--before--' . PHP_EOL;
    }

    /**
     * 控制器执行之后调用方法
     */
    public function after()
    {
        //echo '--after--' . PHP_EOL;
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param array $data 返回接口数据
     * @param int $status 接口状态码
     * @param string $jumpUrl 页面跳转地址
     * @return void
     */
    protected function error($message = '', $data = [], $status = 500, $jumpUrl = '')
    {
        return Response::getInstance()->error($message, $data, $status, $jumpUrl);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param array $data 返回接口数据
     * @param int $status 接口状态码
     * @param string $jumpUrl 页面跳转地址
     */
    protected function success($message = '', $data = [], $status = 200, $jumpUrl = '')
    {
        return Response::getInstance()->success($message, $data, $status, $jumpUrl);
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data, $type = 'json', $json_option = 256)
    {
        return Response::getInstance()->ajaxReturn($data, $type, $json_option);
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param  mixed $name 要显示的模板变量
     * @param  mixed $value 变量的值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        Template::getInstance()->assign($name, $value);
    }

    /**
     * 显示视图文件,传入的文件名不带文件后缀
     * @param string $filename 视图文件名
     * type: admin@Index/index  模块@控制器/方法名
     *       Index/index 或者 Index.index 控制器/方法名
     *       index  只有视图文件名，无文件后缀
     *       传空值，默认访问的模块@控制器/方法名
     */
    public function display($filename = '')
    {
        Template::getInstance()->display($filename);
    }
}