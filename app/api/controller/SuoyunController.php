<?php

namespace  app\api\controller;

class SuoyunController
{

    protected $apiUrl = 'http://119.45.131.102:7001/index.do?action=api_order_fee_submit';

    protected $params = [
        "app_key"           =>  656602,
        "app_secret"        =>  "E61294A5EE57D00B1EBF8129DBF5AD03",
        "order_agent_id"    =>  64992287399
    ];

    //suoyun
    public function recharge(){
        var_dump('recharge');

        $order_agent_bill       = "";   //订单编号
        $order_agent_back_url   = "";   //回调地址 ,当此订单得到状态报告时,平台会回调此地址.需要对此地址进行urlencode编码
        $order_tel              = "";   //终端的电话号码,如13801234567
        $fee_face_price         = 1;    //话费面值,以元为单位，纯数字 例：如果是10元，就传10
        $timestamp              = floor(microtime(true) * 1000);

        $params = array_merge($this->params,
            compact('order_agent_bill','order_agent_back_url','order_tel','fee_face_price','timestamp')
        );
        $app_sign = $this->generateAppSign($params);
        $params['app_sign'] = $app_sign;

        $url = $this->apiUrl .'&'. http_build_query($params);

        var_dump($url);
        $res = $this->curl_get($url);
        var_dump($res);

    }

    private function generateAppSign(array $params): string
    {
        $keys = ['app_key','app_secret', 'order_agent_id','timestamp','order_agent_bill','order_tel'];
        $str  = '';
        foreach ($keys as $key) {
            $str .= $params[$key];
        }
        return md5($str);
    }

    function curl_get($url){
        $curl = curl_init();                                // cURL初始化
        curl_setopt($curl, CURLOPT_URL, $url) ;             // 设置访问网页的URL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   // 执行之后不直接打印出来
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($curl);                         // 执行
        if (!$output){
            return json_encode(['errno'=>curl_errno($curl),'error'=>curl_error($curl)]);
        }
        curl_close($curl);                                  // 关闭cURL
        return $output;
    }

}