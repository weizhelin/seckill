<?php
namespace  app\wechat\controller;
use EasyWeChat\Factory;
class CheckController
{

    public function do(){
        $config = [
            'app_id' => 'wx0254e60d1dfbaf75',
            'secret' => '429d18e21031140128649718870497b3',
            'token' => 'TestToken',
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            //...
        ];

        $app = Factory::officialAccount($config);

        $response = $app->server->serve();
        return $response->send();
    }

}