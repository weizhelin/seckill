<?php


namespace app\controller;


class JsonController extends BaseController
{
    public function do(){

        $jsonContent = '{"result":"{\"msg\":\"查询成功\",\"data\":{\"stationCode\":\"32950741\",\"receivableAmt\":44.12,\"couponAmt\":0.00,\"linkedOrderNo\":null,\"bizType\":\"20\",\"backMsgId\":null,\"orderGoodsList\":[{\"stationCode\":\"32950741\",\"receivableAmt\":44.12,\"ttc\":\"91216\",\"orderNo\":\"32950741SM2022051050151\",\"literSum\":1358452.49,\"salePrice\":8.42,\"isDelete\":false,\"saleCount\":5.24,\"origReturnNo\":\"\",\"tranNo\":\"329507410062022051011402091216\",\"ogdId\":\"32950741SM2022051050151101\",\"isOils\":1,\"saleAmt\":44.12,\"tranTime\":\"2022-05-10 11:40:20\",\"ctc\":\"12048\",\"nozzleNo\":\"006\",\"discountsAmt\":0.00,\"goodsCode\":\"60518722\",\"officialReceiptsAmt\":44.12,\"goodsName\":null,\"asn\":\"1000413500000140348\"}],\"csrSmy\":null,\"city\":\"SMF0\",\"orderStatus\":\"99\",\"crmCardNo\":null,\"editTime\":\"2022-05-10 11:42:24\",\"saleDate\":\"2022-05-10 11:40:20\",\"cardNo\":null,\"rmsStationCode\":\"32950741\",\"posType\":\"3002\",\"tranType\":\"100\",\"pointAmt\":0.00,\"orderDiscountsList\":[],\"carNo\":null,\"orgCode\":null,\"isSecond\":0,\"discountsAmt\":0.00,\"stationName\":\"顶郊加油站\",\"companyCode\":\"4260\",\"editor\":\"sspczmpays\",\"orderSource\":null,\"creator\":\"sspczmpays\",\"orderPayList\":[{\"stationCode\":\"32950741\",\"zfctc\":null,\"extraData\":null,\"couponPubContract\":null,\"accmarkstr\":null,\"customerCode\":null,\"compName\":null,\"opdId\":\"32950741SM2022051050151201\",\"pointAmt\":0.00,\"payNo\":\"S35000771202205101140208921239\",\"pointPubCode\":null,\"btradeno\":null,\"transactionorgcode\":\"35\",\"sourcetype\":null,\"accmark\":null,\"mchcode\":null,\"sourceid\":null,\"couponPubCode\":null,\"accountDate\":\"\",\"orderNo\":\"32950741SM2022051050151\",\"payModeCode\":\"05002023\",\"isCouponPay\":false,\"payMode\":\"聚合支付微信\",\"isDelete\":false,\"orgcode\":null,\"subopenid\":null,\"bizScene\":\"\",\"payAccount\":\"13799999991\",\"payAmt\":44.12,\"couponValue\":null,\"transactionmchcode\":null,\"receSaleCode\":null,\"subaccid\":null,\"couponCode\":null}],\"orderNo\":\"32950741SM2022051050151\",\"bizSystem\":null,\"isDelete\":false,\"transactionOrgCode\":\"35\",\"bizScene\":\"201\",\"posNo\":\"00\",\"userName\":null,\"userId\":null,\"externalOrderNo\":\"S3500077120220510114020892123907\",\"sysProvider\":\"SM\",\"mobilePhone\":\"13799999991\",\"icProviceCode\":\"35\",\"bosStationCode\":\"32950741\",\"createTime\":\"2022-05-10 11:42:24\",\"district\":\"F00I\",\"tranAmt\":44.12,\"officialReceiptsAmt\":44.12},\"retCode\":1}"}';
        $arr = $this->parseJson($jsonContent);

        $orderInfo = $this->parseOrderInfo($arr);

        p($orderInfo);

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