<?php

namespace app\admin\controllers;

use app\admin\models\AdminModel;
use phpframe\Controller;
use phpframe\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->_skip_action = ['index', 'add'];
    }

    public function index()
    {
        echo "Hello Module admin!";
    }

    public function add(Request $request)
    {
        $username = $request->post('username', '');
        $passwd = $request->post('passwd', '');

        if (!$username || !$passwd) {
            $this->error('用户名或密码不能为空~');
        }

        $model = new AdminModel();
        $bool = $model->where("username='{$username}'")->find();
        if ($bool) {
            $this->error('用户名：' . $username . ' 已经注册，请更换其他用户名~');
        }

        $salt = get_random_str(6);
        $passwd = md5(md5($passwd) . $salt);
        $data = [
            'username' => $username,
            'password' => $passwd,
            'salt' => $salt,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $ret = $model->insert($data);
        //echo $model->getLastSQL();
        if ($ret) {
            $this->success('恭喜你，注册成功！');
        }
        $this->error('不好意思，注册失败了~');
    }

    public function edit(Request $request)
    {

    }
}