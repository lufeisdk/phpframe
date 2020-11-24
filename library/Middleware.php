<?php

namespace phpframe;

class Middleware
{
    //绑定的中间件
    private $stack = [];

    /**
     * 导入中间件
     * @access public
     * @param  array $middlewares
     */
    public function import(array $middlewares = [])
    {
        foreach ($middlewares as $middleware) {
            $this->add($middleware);
        }
    }

    /**
     * 注册中间件
     * @access public
     * @param  mixed $middleware
     */
    public function add($middleware)
    {
        if (is_null($middleware)) {
            return;
        }

        if ($middleware) {
            $this->stack[] = $middleware;
        }
    }

    /**
     * 清除中间件
     * @access public
     */
    public function clear()
    {
        $this->stack = [];
    }

    /**
     * 执行函数
     * @param mix $request 请求数据
     * @return mix
     */
    public function run($handler)
    {
        foreach (array_reverse($this->stack) as $middleware) {
            $log = function ($handler) use ($middleware) {
                return function ($request) use ($handler, $middleware) {
                    $class = new $middleware();
                    return $class->handle($request, $handler);
                };
            };
            $handler = $log($handler);
        }
        return $handler;
    }
}