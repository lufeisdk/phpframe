<?php

namespace app\api\models;

use phpframe\Model;

class UserModel extends Model
{
    protected $_name = 'user';

    public function __construct($tagName = 'default')
    {
        parent::__construct($tagName);

        $this->table($this->_name);
    }

    /**
     * 获取用户信息
     * @param string $where 查询条件
     * @param bool $strip 是否过滤账户密码及salt值
     * @return null|Array   返回用户数据
     */
    public function getUserInfo($where = '', $strip = true)
    {
        if ($where) {
            $user = $this->where($where)->find();
            if ($strip) {
                unset($user['password'], $user['salt']);
            }
            return $user;
        }
        return null;
    }
}