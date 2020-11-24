<?php

namespace app\admin\controllers;

use app\admin\models\AdminModel;
use phpframe\Controller;
use phpframe\Request;
use phpframe\Session;
use util\captcha\Captcha;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->_skip_action = ['index', 'logout', 'verify'];
    }

    // 登录首页及验证
    public function index(Request $request)
    {
        if ($request->isAjax()) {
            $keys = [
                'username' => '用户名',
                'password' => '密码',
                'verify' => '验证码',
            ];
            foreach ($request->post() as $key => $item) {
                if (empty($item)) {
                    $this->error($keys[$key] . '不能为空~');
                }
            }

            $username = $request->post('username');
            $passwd = $request->post('password');
            $verify = $request->post('verify');
            if (!(new Captcha())->check($verify)) {
                $this->error('验证码输入错误~');
            }

            $model = new AdminModel();
            $info = $model->getAdminInfo("username='{$username}'", false);
            if (!$info) {
                $this->error('用户名：' . $username . ' 不存在~');
            }

            $passwd = md5(md5($passwd) . $info['salt']);
            if ($passwd != $info['password']) {
                $this->error('密码输入错误~');
            }
            unset($info['password'], $info['salt']);
            Session::getInstance()->set('AdminInfo', $info);
            $this->success('登录成功');
        }
        $this->display();
    }

    // 退出登录
    public function logout()
    {
        Session::getInstance()->clear();
        url('/admin/login/index');
    }

    // 验证码
    public function verify()
    {
        $config = [
            'codeSet' => '2345678abcdefhABCDEFGH',
            'fontSize' => 28,   // 验证码字体大小(px)
            'length' => 4,      // 验证码位数
        ];
        $captcha = new Captcha($config);
        $captcha->entry();
    }
}