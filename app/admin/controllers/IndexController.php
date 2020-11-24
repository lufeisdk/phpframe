<?php

namespace app\admin\controllers;

use app\admin\models\AdminModel;
use phpframe\Controller;
use phpframe\Database;
use phpframe\Model;
use phpframe\Request;
use phpframe\Session;

class IndexController extends Controller
{
    public function __construct()
    {
        //$this->_skip_action = ['index', 'verify'];
    }

    public function index()
    {
        $session = Session::getInstance()->get('AdminInfo');
        var_dump($session);
        //Session::getInstance()->clear();
        $text = 'Hello World!';
        $this->assign('text', $text);
        $this->assign('name', 'twig');
        $this->display("admin@Index.index.html");
    }

    public function user(Request $request)
    {
        var_dump($request->action());
        $text = 'Hello User!';
        $this->assign('text', $text);
        $this->display();
    }

    public function test(Request $request)
    {
        var_dump($request->action());
        $text = 'Hello Test!';
        $this->assign('text', $text);
        $this->display("user");
    }

    public function db()
    {
//        $db = Database::getInstance();
//        $ret = $db->getTables();
//        pp($ret);

        $model = new AdminModel();
        //var_dump($model->prefix());
        //var_dump($model->table('test'));
        //$ret = $model->field('id,username')->where('id< 10')->find();
//        $ret = $model->where('id< 10')->column('username');
//        echo $model->getLastSQL();
//        pp($ret);
//        $ret = $model->where('id< 10')->find('id,username');
//        echo $model->getLastSQL();
//        pp($ret);
//        $rets = $model->where('id< 10')->select('id,username');
//        echo $model->getLastSQL();
//        pp($rets);

//        $data = [
//            'username' => 'tom',
//            'nickname' => 'Tom',
//            'password' => '123456',
//            'salt' => '127345'
//        ];
//        $ret = $model->insert($data);
//        //echo $model->getLastSQL();
//        var_dump($ret);

//        $ret = $model->where('id > 1')->delete();
//        echo $model->getLastSQL();
//        var_dump($ret);

//        $data = [
//            'username' => 'tom',
//        ];
//        $ret = $model->where("id=10")->update($data);
//        echo $model->getLastSQL();
//        var_dump($ret);

//        $db = Database::getInstance('yssy');
//        # 查询配置数据库的所有数据库表
//        $ret = $db->getTables();
//        # 查询指定数据库的所有数据库表
//        $ret = $db->getTables('gm_log');
//        pp($ret);

//        $db = Database::getInstance('sqlsrv');
//        # 查询配置数据库的所有数据库表
//        $ret = $db->getTables();
//        pp($ret);
    }
}