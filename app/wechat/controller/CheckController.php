<?php
namespace  app\wechat\controller;
use EasyWeChat\Factory;
class CheckController
{

    public function do(){
        $config = [
            'app_id' => '',
            'secret' => '',
            'token' => '',
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            //...
        ];
        $config = array_merge($config,config('wechat'));

        $app = Factory::officialAccount($config);

        $response = $app->server->serve();
        return $response->send();
    }

}