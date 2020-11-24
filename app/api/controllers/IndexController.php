<?php

namespace app\api\controllers;

use phpframe\Config;
use phpframe\Controller;
use phpframe\Cookie;
use phpframe\Jwt;
use phpframe\Request;
use phpframe\Session;
use util\apidoc\ApiDoc;

class IndexController extends Controller
{
    # 引入trait
    use \phpframe\common\traits\Controller;

    public function __construct()
    {
        $this->_skip_action = ['index'];

        //$this->_bind_action = ['user'];
    }

    /**
     * 测试
     * @param Request $request
     */
    public function index(Request $request)
    {
        $doc = ApiDoc::getInstance()->make();
        echo $doc;
        die;

        echo '<pre>';
        Session::getInstance()->set('name', '123');
        var_dump($request->session('name'));
        Cookie::getInstance()->set('var', '23456');
        var_dump($request->cookie('var'));
        //print_r($_SERVER);
        //var_dump($request->ip());
        var_dump($request->param());
        var_dump($request->input());
        //var_dump($request->server());
        //var_dump($request->domain());
        //var_dump($request->query());
        //var_dump($request->host(true));
        echo 'Hello World, API!';
    }

    public function user(Request $request)
    {
        var_dump($request->module());
        var_dump($request->controller());
        var_dump($request->action());
    }

    public function jwt()
    {
        $key = Config::get('api_token', 'app', BIND_MODULE);

        //测试和官网是否匹配begin
        $payload = array('sub' => 'phpframe.tpl', 'id' => 1, 'name' => 'tom', 'iat' => time());
        $jwt = new Jwt;
        $token = $jwt->setKey($key)->getToken($payload);
        echo "<pre>";
        echo $token;

        //对token进行验证签名
        $getPayload = $jwt->verifyToken($token);
        echo "<br><br>";
        var_dump($getPayload);
        echo "<br><br>";
    }
}