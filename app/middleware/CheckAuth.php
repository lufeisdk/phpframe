<?php

namespace app\middleware;

//use phpframe\Config;
use phpframe\Jwt;
use phpframe\Response;

class CheckAuth
{
    public function handle($request, \Closure $next)
    {
        $token = $request->post('token', '', 'strip_tags');

        if (!$token) {
            Response::getInstance()->error('缺少参数信息~');
        }

        //$key = Config::get('api_token', 'app', BIND_MODULE);
        //$getPayload = (new Jwt())->setKey($key)->verifyToken($token);
        $getPayload = (new Jwt())->verifyToken($token);
        if (false === $getPayload) {
            Response::getInstance()->error("接口验证失败~");
        }

        $next($request);
    }
}
