<?php


namespace app\controller;


class MqJsonController extends BaseController
{


    public function do(){
    	$json = '{"result":"{\"msg\":\"查询成功\",\"data\":{\"stationCode\":\"32550138\",\"receivableAmt\":20.00,\"couponAmt\":0.00,\"linkedOrderNo\":null,\"bizType\":\"20\",\"backMsgId\":null,\"orderGoodsList\":[{\"stationCode\":\"32550138\",\"receivableAmt\":20.00,\"ttc\":null,\"orderNo\":\"32550138PCITC2022012150000\",\"literSum\":0.00,\"salePrice\":2.22,\"isDelete\":false,\"saleCount\":9.01,\"origReturnNo\":\"\",\"tranNo\":null,\"ogdId\":\"32550138PCITC2022012150000101\",\"isOils\":1,\"saleAmt\":20.00,\"tranTime\":\"2022-01-21 16:19:19\",\"ctc\":\"5218\",\"nozzleNo\":\"007\",\"discountsAmt\":0.00,\"goodsCode\":\"60514941\",\"officialReceiptsAmt\":20.00,\"goodsName\":\"95号京标(Ⅵ)车用汽油\",\"asn\":\"1000419900000602115\"}],\"csrSmy\":\"3v5s7h07kz3xwk5kt115hwzk9m5z13mzm7v7kzxxt1zzsmz1twkt575zh137z599\",\"city\":\"BJDB\",\"orderStatus\":\"99\",\"crmCardNo\":null,\"editTime\":\"2022-01-21 16:15:12\",\"saleDate\":\"2022-01-21 16:19:19\",\"cardNo\":null,\"rmsStationCode\":\"32550138\",\"posType\":\"2001\",\"tranType\":\"100\",\"pointAmt\":0.00,\"orderDiscountsList\":[],\"carNo\":\"京A84552\",\"orgCode\":\"11\",\"isSecond\":1,\"discountsAmt\":0.00,\"stationName\":\"东北南环加油站\",\"companyCode\":\"2690\",\"editor\":\"f5bcbf71290de9937402a44a800b3996\",\"orderSource\":\"YKQG\",\"creator\":\"f5bcbf71290de9937402a44a800b3996\",\"orderPayList\":[{\"stationCode\":\"32550138\",\"zfctc\":null,\"extraData\":null,\"couponPubContract\":null,\"accmarkstr\":\"基本账户\",\"customerCode\":null,\"compName\":null,\"opdId\":\"32550138PCITC2022012150000201\",\"pointAmt\":0.00,\"payNo\":\"3255013811522012116143721532\",\"pointPubCode\":null,\"btradeno\":\"3255013811522012116143721532\",\"transactionorgcode\":\"11\",\"sourcetype\":null,\"accmark\":\"1\",\"mchcode\":null,\"sourceid\":null,\"couponPubCode\":null,\"accountDate\":\"\",\"orderNo\":\"32550138PCITC2022012150000\",\"payModeCode\":\"02001014\",\"isCouponPay\":false,\"payMode\":\"电子钱包\",\"isDelete\":false,\"orgcode\":\"11\",\"subopenid\":null,\"bizScene\":\"\",\"payAccount\":\"ac157f660ede41ac972931ec6743fe4b\",\"payAmt\":20.00,\"couponValue\":null,\"transactionmchcode\":null,\"receSaleCode\":null,\"subaccid\":null,\"couponCode\":null}],\"orderNo\":\"32550138PCITC2022012150000\",\"bizSystem\":\"\",\"isDelete\":false,\"transactionOrgCode\":\"35\",\"bizScene\":\"401\",\"posNo\":\"a30e8f9b4533d211\",\"userName\":\"游林峰\",\"userId\":\"f5bcbf71290de9937402a44a800b3996\",\"externalOrderNo\":\"32550138115220121161437215\",\"sysProvider\":\"PCITC\",\"mobilePhone\":\"13427526526\",\"icProviceCode\":\"35\",\"bosStationCode\":\"32550138\",\"createTime\":\"2022-01-21 16:15:12\",\"district\":\"BJS2\",\"tranAmt\":20.00,\"officialReceiptsAmt\":20.00},\"retCode\":1}"}';

    	$arr = $this->parseJson($json);
        $orderInfo = $this->parseOrderInfo($arr);

        //p($arr);
        p($orderInfo);
    }	


 	/**
     * 将订单数据数据存入数据库
     * @param array $arr
     * @return array
     */
    protected function parseOrderInfo($arr): array
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


    protected function isJson($data = '', $assoc = false): bool
    {
        $data = json_decode($data, $assoc);
        if (($data && (is_object($data))) || (is_array($data) && !empty($data))) {
            return true;
        }
        return false;
    }

    /**
     * JSON数组解析成数组
     * @param string $json
     * @return array
     */
    protected function parseJson($json): array
    {
        if (empty($json)) {
            return [];
        }
        $arr = json_decode($json, true);
        foreach ($arr as $key => &$value) {
            if ($this->isJson($value)) {
                $value = json_decode($value, true);
            }
        }
        return $arr;
    }
}