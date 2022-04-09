<?php


namespace app\controllers;


use app\util\Rsa;

class IconvController   extends BaseController
{

    protected $pub = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCMeYJ2gw6prrT3DG4R5PcMmYchaiPCiRPHkagDufXorqmAV+rfb9qEeMjriInnw16U2HJMZowIJHNW4dJPeIP/soc6cTGIboGJk3Q9PY7bRBoy0utFpmoAxIW7Msq0dhQdGl1nU2rsjw/r+UjAQAwC90mB/RU2i8Dr3cQG6Ro7WQIDAQAB
-----END PUBLIC KEY-----';

    protected $pri = '-----BEGIN PRIVATE KEY-----
MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAIx5gnaDDqmutPcMbhHk9wyZhyFqI8KJE8eRqAO59eiuqYBX6t9v2oR4yOuIiefDXpTYckxmjAgkc1bh0k94g/+yhzpxMYhugYmTdD09jttEGjLS60WmagDEhbsyyrR2FB0aXWdTauyPD+v5SMBADAL3SYH9FTaLwOvdxAbpGjtZAgMBAAECgYAm28IEKJLYy3RR1sVn+89/U8T2sFa+DI9FKHyyszFcMVzAHRHixx1KgeMKjJWhYAD86bcMfV2FLxgc05EK3MqeeV4/qPUw9Q1WsO52j0opvyqRCa99/+s4kyfZDwu9afdWSj6xO7y6u6FjRZfnKRPNsEBltdzyYmpSV+oB3kpOdQJBAOKSjUmA7hsF09HvipodP/eGv4VvBluJRAJzbSx+zJxRQNmiuDXpdd+hFwk/uZzdAFcSw3as8AQTD3Xj/kYxo68CQQCeuDzur3XR4FcK3FqrAJ+zQnalUyGas24c4LkMoCUrEtL3lSTD+p2G8j5PGHMWUBXCUJ1PB46gdzdMqLJPc2t3AkEAuanuLXWBqJM16KqqoW+mo2fAOc+pHgl1uaxsojGl6dKLmcxFt6f/96lB/0pBB9HyHWg61F0SscQMv0Z9b3ft8QJBAIGd06TYhU8v9eVYrnKXv9OUo2+/w+GiRnouyvAUmEXkyYSGt8+UCrD5gwj03oeKPzrAurafZUqGERL5cSSRDWECQQC97JhLSvevf3GIYTnkuwTZsLhEwoRdH5ow2PC6qivQO0X5EZ0Rf350vqZcOKjxpT8BwvZj4BOctuIzjRdJ/IjS
-----END PRIVATE KEY-----';

    protected $key  =   "654dbd3f3549448aa780c925abcbb42d";



    public function test(){
        $str = '中华人民共和国';

        $rsa = new Rsa($this->pub,$this->pri,Rsa::PKCS1);

        $encrypt = $rsa->encrypt($str);
        var_dump($encrypt);
    }

}