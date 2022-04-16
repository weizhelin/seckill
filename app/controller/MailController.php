<?php


namespace app\controller;


use app\util\Mail;

class MailController
{
    public function index(){
        $rs = Mail::send(
            ['address'  =>  '360093893@qq.com','name'=>'darling'],
            "测试邮件",
            '<p style="color: #00b4ef"> <a href="http://www.baidu.com">百度</a> 测试邮件内容</p>'
        );
        var_dump($rs);
    }
}