<?php
namespace master\controller;

class Log extends \Core\Controller\Common
{
    public function login(){
        $this->assign('title','用户登录');
        $this->display('log/login.html');
    }

    /**
     * 功能：执行登录操作
     * param user 用户名
     * param pwd 密码
    */
    public function dologin(){
        if (!isset($_POST['pwd']) || !isset($_POST['user'])) {
            json_reply(400,'参数错误');
            exit;
        }
        $username = $_POST['user'];
        $userModel = model('User');

        $map = array();
        $map['username'] = $username;
        $userinfo = $userModel->where($map)->find();
        if (!$userinfo) {
            json_reply(400,'无此用户');
            exit;
        }

        if ($userinfo['password'] != md5(md5($_POST['pwd']).'shenduan')) {
            json_reply(400,'密码错误');
        }

        $_SESSION['userinfo'] = $userinfo;
        json_reply(200,'登录成功');

    }

    /**
     * 功能：退出登录
    */
    public function logout(){
        session_start();
        unset($_SESSION);
        session_destroy();
        switch (URL_MODE) {
            case 'PATHINFO':
                header("location:/index.php/Log/login");
                break;
            case 'URL':
                header("location:/index.php?c=Log&a=login");
                break;
        }
    }
}