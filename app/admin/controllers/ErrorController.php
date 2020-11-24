<?php

namespace app\admin\controllers;

use phpframe\Controller;
use phpframe\Request;

class ErrorController extends Controller
{
    public function __construct()
    {
        $this->_skip_action = ['index'];
    }

    public function index(Request $request)
    {
        $referer = $request->referrer() ?: '/admin/login/index';
        $msg = $request->get('msg') ?? '页面错误~';
        $this->assign('msg', $msg);
        $this->assign('url', $referer);
        $this->display();
    }
}