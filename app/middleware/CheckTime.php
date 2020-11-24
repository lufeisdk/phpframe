<?php

namespace app\middleware;

class CheckTime
{
    public function handle($request, \Closure $next)
    {
        $next($request);

        //echo PHP_EOL . '--后置中间件--' . PHP_EOL;
    }
}
