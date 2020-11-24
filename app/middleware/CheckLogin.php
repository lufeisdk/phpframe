<?php

namespace app\middleware;

use phpframe\Session;

class CheckLogin
{
    public function handle($request, \Closure $next)
    {
        $session = Session::getInstance()->get('AdminInfo');
        if (empty($session)) {
            url('/admin/error/index', '用户还未登录，请先登录~');
            return;
        }

        $next($request);
    }
}