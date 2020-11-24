<?php

namespace app\api\controllers;

use app\api\models\UserModel;
use phpframe\Controller;
use phpframe\Request;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->_skip_action = ['signin'];
    }

    /**
     * 用户注册接口
     * @url api/register/signin
     * @method POST
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $passwd 密码
     * @return json $json {'code':200,'msg':'恭喜你，注册成功'}
     * @return json $json {'code':500,'msg':'不好意思，注册失败了'}
     * @return json $json {'code':500,'msg':'用户名或密码不能为空'}
     * @return json $json {'code':500,'msg':'用户名已经注册，请更换其他用户名'}
     */
    public function signIn(Request $request)
    {
        $username = $request->post('username', '', 'strip_tags');
        $nickname = $request->post('nickname', '', 'strip_tags');
        $passwd = $request->post('passwd', '', 'strip_tags');

        if (!$username || !$passwd) {
            $this->error('用户名或密码不能为空~');
        }

        $user_model = new UserModel();
        $bool = $user_model->where("username='{$username}'")->find();
        if ($bool) {
            $this->error('用户名：' . $username . ' 已经注册，请更换其他用户名~');
        }

        $salt = get_random_str(6);
        $passwd = md5(md5($passwd) . $salt);
        $data = [
            'username' => $username,
            'nickname' => $nickname,
            'password' => $passwd,
            'salt' => $salt,
            'ip' => $request->ip(),
            'regtime' => time(),
        ];

        $ret = $user_model->insert($data);
        if ($ret) {
            $this->success('恭喜你，注册成功！');
        }
        $this->error('不好意思，注册失败了~');
    }
}