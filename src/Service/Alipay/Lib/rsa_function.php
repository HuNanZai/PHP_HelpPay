<?php
namespace HuNanZai\HelpPay\Service\Alipay\Lib;

/**
 * RSA签名
 *
 * @param $data 待签名数据
 * @param $private_key_path 商户私钥文件路径
 *
 * @return 签名结果
 */
function rsaSign($data, $private_key_path)
{
    $priKey = file_get_contents($private_key_path);
    $res    = openssl_get_privatekey($priKey);
    openssl_sign($data, $sign, $res);
    openssl_free_key($res);
    //base64编码
    $sign = base64_encode($sign);

    return $sign;
}

/**
 * RSA验签
 *
 * @param $data 待签名数据
 * @param $ali_public_key_path 支付宝的公钥文件路径
 * @param $sign 要校对的的签名结果
 *
 * @return 验证结果
 */
function rsaVerify($data, $ali_public_key_path, $sign)
{
    $pubKey = file_get_contents($ali_public_key_path);
    $res    = openssl_get_publickey($pubKey);
    $result = (bool)openssl_verify($data, base64_decode($sign), $res);
    openssl_free_key($res);

    return $result;
}

/**
 * RSA解密
 *
 * @param $content 需要解密的内容，密文
 * @param $private_key_path 商户私钥文件路径
 *
 * @return 解密后内容，明文
 */
function rsaDecrypt($content, $private_key_path)
{
    $priKey = file_get_contents($private_key_path);
    $res    = openssl_get_privatekey($priKey);
    //用base64将内容还原成二进制
    $content = base64_decode($content);
    //把需要解密的内容，按128位拆开解密
    $result = '';
    for ($i = 0; $i < strlen($content) / 128; $i++) {
        $data = substr($content, $i * 128, 128);
        openssl_private_decrypt($data, $decrypt, $res);
        $result .= $decrypt;
    }
    openssl_free_key($res);

    return $result;
}