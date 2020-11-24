<?php

namespace app\api\controllers;

use app\api\models\UserModel;
use phpframe\Controller;
use phpframe\Jwt;
use phpframe\Request;

class UserController extends Controller
{
    public function __construct()
    {
        //$this->_skip_action = ['info'];
    }

    /**
     * 用户信息接口
     * @url api/user/info
     * @method POST
     * @param string $token 接口token值
     * @return json $json {'code':200,'msg':'ok',"data":{"用户数据"}}
     * @return json $json {'code':500,'msg':'用户查询失败'}
     * @return json $json {'code':500,'msg':'用户信息获取失败'}
     */
    public function info(Request $request)
    {
        $getPayload = Jwt::getPayload();
        if (!$getPayload) {
            $this->error("用户信息获取失败~");
        }

        $uid = $getPayload['id'];
        $user_model = new UserModel();
        $user = $user_model->getUserInfo("id='{$uid}'");
        if (empty($user)) {
            $this->error('用户查询失败~');
        }
        $this->success('ok', $user);
    }

    /**
     * 更新密码接口
     * @url api/user/updatePasswd
     * @method POST
     * @param string $passwd 密码
     * @param string $passwd_confirm 确认密码
     * @return json $json {'code':200,'msg':'密码更新成功'}
     * @return json $json {'code':500,'msg':'密码更新失败'}
     * @return json $json {'code':500,'msg':'用户信息查询失败'}
     * @return json $json {'code':500,'msg':'两次密码输入不一致'}
     * @return json $json {'code':500,'msg':'密码输入不能为空'}
     */
    public function updatePasswd(Request $request)
    {
        $passwd = $request->post('passwd', '');
        $passwd_confirm = $request->post('passwd_confirm', '');
        if (!$passwd || !$passwd_confirm) {
            $this->error('密码输入不能为空~');
        }

        if ($passwd != $passwd_confirm) {
            $this->error('两次密码输入不一致~');
        }

        $getPayload = Jwt::getPayload();
        if (!$getPayload) {
            $this->error("用户信息获取失败~");
        }

        $uid = $getPayload['id'];
        $user_model = new UserModel();
        $where = "id='{$uid}'";
        $user = $user_model->getUserInfo($where, false);
        if (empty($user)) {
            $this->error('用户信息查询失败~');
        }

        $passwd = md5(md5($passwd) . $user['salt']);
        $ret = $user_model->where($where)->update(['password' => $passwd]);
        if ($ret) {
            $this->success('密码更新成功~');
        }
        $this->error('密码更新失败~');
    }

    /**
     * 更新头像接口
     * @url api/user/updateAvatar
     * @method POST
     * @param string $avatar 头像
     * @return json $json {'code':200,'msg':'头像更新成功'}
     * @return json $json {'code':500,'msg':'头像更新失败'}
     * @return json $json {'code':500,'msg':'用户信息获取失败'}
     * @return json $json {'code':500,'msg':'参数错误'}
     */
    public function updateAvatar(Request $request)
    {
        $file = $request->file('avatar');

        if (empty($file)) {
            $this->error('参数错误~');
        }

        $getPayload = Jwt::getPayload();
        if (!$getPayload) {
            $this->error("用户信息获取失败~");
        }

        $uid = $getPayload['id'];

        $info = $file->move(ROOT_PATH . '/upload/avatar/' . $uid, false);
        $filename = $info->getSaveName();
        if ($filename) {

            $user_model = new UserModel();
            $where = "id='{$uid}'";
            $ret = $user_model->where($where)->update(['avatar' => $filename]);
            if ($ret) {
                $this->success('头像更新成功~');
            }
        }

        $this->error('头像更新失败~');
    }

    /**
     * 更新用户信息接口
     * @url api/user/editinfo
     * @method POST
     * @param string $nickname 昵称
     * @param string $mobile 手机号
     * @param string $email 邮箱
     * @return json $json {'code':200,'msg':'用户信息更新成功'}
     * @return json $json {'code':500,'msg':'用户信息更新失败'}
     * @return json $json {'code':500,'msg':'用户信息获取失败'}
     * @return json $json {'code':500,'msg':'参数错误'}
     */
    public function editInfo(Request $request)
    {
        $nickname = $request->post('nickname', '');
        $mobile = $request->post('mobile', '');
        $email = $request->post('email', '');

        if (!$nickname && !$mobile && !$email) {
            $this->error('参数错误~');
        }

        if ($mobile && !preg_match("/^1[3-9]{1}[0-9]{9}$/", $mobile)) {
            $this->error('手机号格式错误~');
        }

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('邮箱地址格式错误~');
        }

        $getPayload = Jwt::getPayload();
        if (!$getPayload) {
            $this->error("用户信息获取失败~");
        }

        $uid = $getPayload['id'];
        $user_model = new UserModel();
        $where = "id='{$uid}'";
        $data = [
            'nickname' => $nickname,
            'mobile' => $mobile,
            'email' => $email
        ];
        $ret = $user_model->where($where)->update($data);
        if ($ret) {
            $this->success('用户信息更新成功~');
        }
        $this->error('用户信息更新失败~');
    }
}