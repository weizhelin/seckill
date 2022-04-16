<?php


namespace app\controller;


use app\models\ProductPackage;

class JsonController extends BaseController
{
    public function do(){

//        $jsonContent = file_get_contents(ROOT_PATH . '35550135PCITC2021122050001.json');
//
//        $arr = $this->parseJson($jsonContent);
//
//        $orderInfo = $this->parseOrderInfo($arr);
//
//        p($orderInfo);

        $packages = (new ProductPackage())->select('product_package','*');
        var_dump($packages);

    }

    /**
     * JSON数组解析成数组
     * @param string $json
     * @return array
     */
    protected function parseJson(string $json): array
    {
        if (empty($json)) {
            return [];
        }
        $arr = json_decode($json, true);
        foreach ($arr as $key => &$value) {
            if ($this->isJson($value)) {
                $value = $this->parseJson($value);
            }
        }
        return $arr;
    }

    protected function isJson($data = '', $assoc = false): bool
    {
        if (is_int($data) || is_array($data) || is_bool($data)){
            return false;
        }
        $data = json_decode($data, $assoc);
        if (($data && (is_object($data))) || (is_array($data) && !empty($data))) {
            return true;
        }
        return false;
    }

    /**
     * 将订单数据数据存入数据库
     * @param array $arr
     * @return array
     */
    protected function parseOrderInfo(array $arr): array
    {
        $order = $arr['result']['data'];
        $subs = [];
        foreach ($order as $k => &$val) {
            if (is_array($val)) {
                $subs[$k] = $val;
                unset($order[$k]);
            }
        }
        return compact('order', 'subs');
    }
}