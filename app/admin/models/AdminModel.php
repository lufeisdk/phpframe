<?php

namespace app\admin\models;

use phpframe\Model;

class AdminModel extends Model
{
    protected $_name = 'admin';

    public function __construct($tagName = 'default')
    {
        parent::__construct($tagName);

        $this->table($this->_name);
    }

    /**
     * 获取管理员信息
     * @param string $where 查询条件
     * @param bool $strip 是否过滤账户密码及salt值
     * @return null|Array   返回用户数据
     */
    public function getAdminInfo($where = '', $strip = true)
    {
        if ($where) {
            $info = $this->where($where)->find();
            if ($strip) {
                unset($info['password'], $info['salt']);
            }
            return $info;
        }
        return null;
    }
}