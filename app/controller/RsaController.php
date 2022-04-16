<?php


namespace app\controller;


class RsaController extends BaseController
{

    protected $priKey;
    protected $pubKey;

    public function __construct()
    {
        parent::__construct();
        $rsaPath = ROOT_PATH . DIRECTORY_SEPARATOR . 'rsa';
        $this->priKey = file_get_contents($rsaPath.DIRECTORY_SEPARATOR . 'pri.key');
        $this->pubKey = file_get_contents($rsaPath.DIRECTORY_SEPARATOR . 'pub.key');
    }

    CONST MAX_BLOCK_LENGTH = 117;

    protected function getContent(){
        $content = '';
        for ($i = 1; $i <= 117; $i++){
            $content .= get_rand_str(10);
        }
        return $content;
    }

    public function test(){
        p(ROOT_PATH);
        $content = $this->getContent();
        p(strlen($content));
        p($content);

        $base64EncodedOpensslEncryptedSplits = $this->encryptWithPriKey($content);
        p($base64EncodedOpensslEncryptedSplits);

        $this->decryptWithPubKey($base64EncodedOpensslEncryptedSplits);
        exit();



    }

    protected function encryptWithPriKey($content): string
    {
        $priKey = $this->priKey;
        $pKey = openssl_pkey_get_private($priKey);

        //将加密对象转码为base64
        $base64EncodedContent = base64_encode($content);
        //将base64编码后的内容，进行分段
        $base64EncodedContentSplits = str_split($base64EncodedContent,self::MAX_BLOCK_LENGTH);

        $opensslEncryptedSplits = '';
        foreach ($base64EncodedContentSplits as $base64EncodedContentSplit){
            openssl_private_encrypt($base64EncodedContentSplit, $opensslEncryptedSplit, $pKey);
            $opensslEncryptedSplits .= $opensslEncryptedSplit;
        }
        return base64_encode($opensslEncryptedSplits);
    }

    protected function decryptWithPubKey($base64EncodedOpensslEncryptedSplits){
        $opensslEncryptedSplits = base64_decode($base64EncodedOpensslEncryptedSplits);

        $opensslEncryptedSplits =  str_split($opensslEncryptedSplits,128);

        $pubKey = openssl_pkey_get_public($this->pubKey);// 资源类型

        $decodedContent = '';
        foreach ($opensslEncryptedSplits as $opensslEncryptedSplit){
            openssl_public_decrypt($opensslEncryptedSplit, $opensslDecryptedSplit, $pubKey);
            $decodedContent .= $opensslDecryptedSplit;
        }
        p($decodedContent);
        p(base64_decode($decodedContent));
    }
}