<?php

namespace app\api\controllers;

use app\api\models\UserModel;
use phpframe\Controller;
use phpframe\Request;
use phpframe\Config;
use phpframe\Jwt;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->_skip_action = ['signon'];
    }

    /**
     * 用户登录接口
     * @url api/login/signon
     * @method POST
     * @param string $username 用户名
     * @param string $passwd 密码
     * @return json $json {'code':200,'msg':'登录成功',"data":{"token":"token值"}}
     * @return json $json {'code':500,'msg':'登录失败'}
     * @return json $json {'code':500,'msg':'用户名或密码不能为空'}
     * @return json $json {'code':500,'msg':'用户名不存在'}
     * @return json $json {'code':500,'msg':'密码输入错误'}
     */
    public function signOn(Request $request)
    {
        $username = $request->post('username', '', 'strip_tags');
        $passwd = $request->post('passwd', '', 'strip_tags');

        if (!$username || !$passwd) {
            $this->error('用户名或密码不能为空~');
        }

        $user_model = new UserModel();
        $user = $user_model->getUserInfo("username='{$username}'", false);
        if (empty($user)) {
            $this->error('用户名：' . $username . ' 不存在~');
        }

        $passwd = md5(md5($passwd) . $user['salt']);
        if ($passwd != $user['password']) {
            $this->error('密码输入错误~');
        }

        // $key = Config::get('api_token', 'app', BIND_MODULE);
        $payload = [
            'id' => $user['id'],
            'name' => $user['username'],
            'iat' => time(),
            'exp' => time() + 7200,
        ];
        // $token = (new Jwt())->setKey($key)->getToken($payload);
        $token = (new Jwt())->getToken($payload);
        if ($token) {
            $this->success('登录成功', ['token' => $token]);
        }
        $this->error('登录失败');
    }
}