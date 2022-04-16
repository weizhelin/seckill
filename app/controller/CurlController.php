<?php


namespace app\controller;


class CurlController
{
    public function post(){
        $url = "http://local.seckill.com/curl/get";
        $data = array(['id'=>rand(1,10),'time'=>time()]);

        $this->api_exec2($url,true,$data);

    }

    static public function curlPostJ($params = array(),$url,$type = 'default',$header = array(),$timeOut = 10){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);

        if($type == 'json'){
            $header = array_merge(array('Content-type:application/json;charset=UTF-8'),$header);
            $params = json_encode($params,JSON_UNESCAPED_UNICODE);
            //print_r($params);exit();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }else{
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $params = http_build_query($params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $data = curl_exec($ch);

        if (curl_errno($ch)) {//出错则显示错误信息
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

    function api_exec($url, $post = false, $params = '')
    {
        $ch = curl_init();
        if (!$post) {
            if ($params) {
                $query = http_build_query($params);
                if (stripos($url, '?') !== false) {
                    $url = $url . "&" . $query;
                } else {
                    $url = $url . "?" . $query;
                }
            }
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (is_array($params)) {
                $query = http_build_query($params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }

        $data = curl_exec($ch);

        if (curl_errno($ch)) {//出错则显示错误信息
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

    static function api_exec2($url, $post = false, $params = '')
    {
        $ch = curl_init();
        if (!$post) {
            if ($params) {
                $query = http_build_query($params);
                if (stripos($url, '?') !== false) {
                    $url = $url . "&" . $query;
                } else {
                    $url = $url . "?" . $query;
                }
            }
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (is_array($params)) {
                $query = http_build_query($params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
        }

        $data = curl_exec($ch);

        if ($errNo = curl_errno($ch)) {//出错则显示错误信息
            $error = curl_error($ch);
            return curl_error($ch);
        }

        curl_close($ch);
        return $data;
    }

    public function get(){
        $post = $_POST;
        $json = json_encode($post);
        file_put_contents('json.log',"\n".date("Ymd His").$json,FILE_APPEND);

    }
}