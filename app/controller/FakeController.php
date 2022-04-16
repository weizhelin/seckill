<?php

namespace app\controller;
header("Content-type:text/html;charset=utf-8");
class FakeController extends BaseController
{

    CONST ENCRYPT_SPLIT_LENGTH = 100;
    CONST DECRYPT_SPLIT_LENGTH = 172;

    protected $pri = '-----BEGIN PRIVATE KEY-----
MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAIx5gnaDDqmutPcMbhHk9wyZhyFqI8KJE8eRqAO59eiuqYBX6t9v2oR4yOuIiefDXpTYckxmjAgkc1bh0k94g/+yhzpxMYhugYmTdD09jttEGjLS60WmagDEhbsyyrR2FB0aXWdTauyPD+v5SMBADAL3SYH9FTaLwOvdxAbpGjtZAgMBAAECgYAm28IEKJLYy3RR1sVn+89/U8T2sFa+DI9FKHyyszFcMVzAHRHixx1KgeMKjJWhYAD86bcMfV2FLxgc05EK3MqeeV4/qPUw9Q1WsO52j0opvyqRCa99/+s4kyfZDwu9afdWSj6xO7y6u6FjRZfnKRPNsEBltdzyYmpSV+oB3kpOdQJBAOKSjUmA7hsF09HvipodP/eGv4VvBluJRAJzbSx+zJxRQNmiuDXpdd+hFwk/uZzdAFcSw3as8AQTD3Xj/kYxo68CQQCeuDzur3XR4FcK3FqrAJ+zQnalUyGas24c4LkMoCUrEtL3lSTD+p2G8j5PGHMWUBXCUJ1PB46gdzdMqLJPc2t3AkEAuanuLXWBqJM16KqqoW+mo2fAOc+pHgl1uaxsojGl6dKLmcxFt6f/96lB/0pBB9HyHWg61F0SscQMv0Z9b3ft8QJBAIGd06TYhU8v9eVYrnKXv9OUo2+/w+GiRnouyvAUmEXkyYSGt8+UCrD5gwj03oeKPzrAurafZUqGERL5cSSRDWECQQC97JhLSvevf3GIYTnkuwTZsLhEwoRdH5ow2PC6qivQO0X5EZ0Rf350vqZcOKjxpT8BwvZj4BOctuIzjRdJ/IjS
-----END PRIVATE KEY-----';

    protected $key  =   "654dbd3f3549448aa780c925abcbb42d";

    protected $pub = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCMeYJ2gw6prrT3DG4R5PcMmYchaiPCiRPHkagDufXorqmAV+rfb9qEeMjriInnw16U2HJMZowIJHNW4dJPeIP/soc6cTGIboGJk3Q9PY7bRBoy0utFpmoAxIW7Msq0dhQdGl1nU2rsjw/r+UjAQAwC90mB/RU2i8Dr3cQG6Ro7WQIDAQAB
-----END PUBLIC KEY-----';


    public function test(){
        $content = file_get_contents("php://input");
        $content = urlencode($content);
        var_dump(urldecode($content));
        var_dump(mb_detect_encoding($content,['UTF-8','ASCII']));
        $encryptData = $this->encryptDataWithPrivateKey($content);
        p(compact('encryptData'));
        $decryptData = $this->decryptValueWithPublicKey($encryptData);
        p(compact('decryptData'));
    }



    /**
     * 模拟银行生成请求参数
    */
    public function fake(){
        $post = $_POST;

        $encrypted = $this->getEncryptedPostData($post);

        $decrypted = $encrypted;
        //对各参数进行解密
        foreach ($decrypted as $key => &$value){
            if ($key == 'sg'){
                continue;
            }
            if (is_numeric($value) && strlen($value) <= 11){
                continue;
            }
            $value = $this->decryptValueWithPrimaryKey($value);
        }

        $timestamp = floor(microtime(true) * 1000);
        echo json_encode(compact('post','encrypted','decrypted','timestamp'));

    }

    protected function getEncryptedPostData($post){
        ksort($post);
        $str = '';
        foreach ($post as $key => &$value){
            if ($key == 'sg'){
                continue;
            }
            if ($key != 'getCodeTime'){
                $value = $this->encryptDataWithPublicKey($value);
            }
            if (!empty($str)){
                $str .= '&';
            }
            $str .= $key.'='.$value;
        }
        $str .= "&key=" . $this->key;
        $sg = strtoupper(md5($str));
        $post['sg'] = $sg;
        $post['str'] = $str;
        return $post;
    }

    /**
     * 组装成功信息
     * @param array $data
     * @param string $errorMsg
     * @param int $errorCode
     */
    protected function returnSuccessMsg($data = [],$errorMsg = "请求成功",$errorCode = 0){
        $return = compact('errorCode','data','errorMsg');
        //$return = $this->getEncryptedMsg($return);
        $this->echoReturnMsg($return);
    }

    /**
     * 组装失败消息
     * @param string $errorMsg
     * @param int $errorCode
     * @param array $data
     */
    protected function returnErrorMsg($errorMsg = "请求失败",$errorCode = 1,$data = []){
        $return = compact('errorCode','data','errorMsg');
        //$return = $this->getEncryptedMsg($return);
        $this->echoReturnMsg($return);
    }

    /**
     * 输出返回消息
     */
    protected function echoReturnMsg($return){
        if (is_array($return)){
            $return = json_encode($return);
        }
        echo $return;
        exit();
    }

    /**
     * 对返回的消息使用私钥进行加密
     */
    protected function getEncryptedMsg($return){
        ksort($return);
        $str = '';
        foreach ($return as $key => &$value){
            if (is_array($value)){
                $value = json_encode($value);
            }
            if (!empty($str)){
                $str .= '&';
            }
            $value = $this->encryptDataWithPrivateKey($value);
            $str .= $key.'='.$value;
        }
        $str .= "&key=" . $this->key;
        $sg = strtoupper(md5($str));
        $sg = $this->encryptDataWithPrivateKey($sg);
        $return['sg'] = $sg;
        return $return;
    }

    /**
     * 1、根据邮储请求参数中的 sg，按 RSA 私钥解密获取 MD5 值
     * 2、将除 sg 外的请求参数，按 3.2 请求加密规则中的第二步、第三步、第四步生成 MD5 值
     * 3、比较两个 MD5 值是否相同，相同验签成功
     * @param $data
     * @return bool
     */
    protected function validatePostDataFromPSBC($data): bool
    {
        $md5 = $this->decryptValueWithPrimaryKey($data['sg']);
        $md5Verify = $this->getMd5FromDataExceptSg($data);
        return $md5 == $md5Verify;
    }


    /**
     * 使用公钥进行加密，以对银行发送过来的数据进行验证
     * @param $data
     * @return false|string
     */
    protected function encryptDataWithPublicKey($data){
        return $this->encryptDataWithRSAKey($data,$this->pub);
    }

    /**
     * 以私钥进行加密以返回给银行
     * @param $data
     * @return false|string
     */
    protected function encryptDataWithPrivateKey($data){
        return $this->encryptDataWithRSAKey($data,$this->pri);
    }

    function strToUtf8($str){
        $encode = mb_detect_encoding($str, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
        if($encode == 'UTF-8'){
            return $str;
        }else{
            return mb_convert_encoding($str, 'UTF-8', $encode);
        }
    }

    protected function encryptDataWithRSAKey($data,$key){
        $isPrivate = (stripos($key,'public') === false);
        if ($isPrivate){
            $pKey = openssl_pkey_get_private($key);
        }else{
            $pKey = openssl_pkey_get_public($key);
        }

        var_dump(mb_detect_encoding($data));
        //
        $base64EncodedData = base64_encode($data);
        var_dump($base64EncodedData);
        $split = str_split($base64EncodedData, self::ENCRYPT_SPLIT_LENGTH);// 1024bit && OPENSSL_PKCS1_PADDING  不大于117即可
        $encode_data = '';
        foreach ($split as $part) {
            var_dump($part);
            if ($isPrivate){
                $isOkay = openssl_private_encrypt($part, $en_data, $pKey);
            }else{
                $isOkay = openssl_public_encrypt($part, $en_data, $pKey);
            }
            if(!$isOkay){
                return false;
            }
            $encode_data .= $en_data;
        }
        return $encode_data;

    }

    /**
     * 使用私钥对加密数据进行解密
     * @param $data
     * @return false|string
     */
    protected function decryptValueWithPrimaryKey($data){
        return  $this->decryptValueWithKey($data,$this->pri);
    }


    /**
     * 使用公钥对加密数据进行解密
     * @param $data
     * @return false|string
     */
    protected function decryptValueWithPublicKey($data){
        return  $this->decryptValueWithKey($data,$this->pub);
    }

    protected function decryptValueWithKey($data,$key){
        $isPrivate = (stripos($key,'public') === false);

        if ($isPrivate){
            $pKey = openssl_pkey_get_private($key);
        }else{
            $pKey = openssl_pkey_get_public($key);
        }
        $split = str_split($data, self::DECRYPT_SPLIT_LENGTH);// 1024bit  固定172

        $base64Data = '';
        foreach ($split as $part) {

            if ($isPrivate){
                $isOkay = openssl_private_decrypt($part, $de_data, $pKey);
            }else{
                $isOkay = openssl_public_decrypt($part, $de_data, $pKey);
            }
            if(!$isOkay){
                return false;
            }
            $base64Data .= $de_data;
        }
        var_dump(compact('base64Data'));
        return base64_decode($base64Data);
    }

}