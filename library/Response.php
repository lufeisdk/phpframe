<?php

namespace phpframe;

class Response
{
    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    private function __construct()
    {

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
    public function error($message = '', $data = [], $status = 500, $jumpUrl = '')
    {
        $data = [
            'msg' => $message,
            'code' => $status,
            'data' => $data,
            'url' => $jumpUrl,
        ];
        $this->ajaxReturn($data);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param array $data 返回接口数据
     * @param int $status 接口状态码
     * @param string $jumpUrl 页面跳转地址
     */
    public function success($message = '', $data = [], $status = 200, $jumpUrl = '')
    {
        $data = [
            'msg' => $message,
            'code' => $status,
            'data' => $data,
            'url' => $jumpUrl,
        ];
        $this->ajaxReturn($data);
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    public function ajaxReturn($data, $type = 'json', $json_option = 256)
    {
        switch (strtoupper($type)) {
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler = 'callback';
                exit($handler . '(' . json_encode($data, $json_option) . ');');
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
            default     :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data, $json_option));
        }
    }
}