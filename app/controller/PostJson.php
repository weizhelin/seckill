<?php


namespace app\controller;


class PostJson extends BaseController
{

    public function index(){
        p(time());
        $api = 'http://121.204.110.49:8123/oilhub/consumeOrder.do';
        p($api);
        $rs = $this->curlPostJ($api,['hi'=>'hello'],'json');
        p($rs);
    }

    /**
     *  curl -sv -H "Content-type: application/json" -X POST http://121.204.110.49:8123/oilhub/consumeOrder.do '{"hello":"world"}'
    */

    protected function curlPostJ($url,$params = array(),$type = 'default',$header = array(),$timeOut = 10){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);

        if($type == 'json'){
            $header = array_merge(array('Content-type:application/json;charset=UTF-8'),$header);
            $params = json_encode($params,JSON_UNESCAPED_UNICODE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }else{
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $params = http_build_query($params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $data = curl_exec($ch);


        if ($errno = curl_errno($ch)) {//出错则显示错误信息
            p($errno);
            $err = curl_error($ch);
            p($err);
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 0) {
                return '发送超时';
            }
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 100) {
                return '错误响应100';
            }
            return print_r(curl_getinfo($ch, CURLINFO_HTTP_CODE), true).'错误信息：'.print_r(curl_error($ch), true);
        }
        curl_close($ch);
        return $data;
    }

}