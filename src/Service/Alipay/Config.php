<?php
namespace HuNanZai\HelpPay\Service\Alipay;

class Config
{
    const NOTIFY_URL = '';
    const RETURN_URL = '';

    const PARTNER = '';                 //合作者身份id 以2088开头的16位纯数字
    const SELLER_ID = self::PARTNER;    //收款支付宝账号
    const SELLER_EMAIL = '';            //卖家的支付宝账户
    //请放在Resource目录下
    const PRIVATE_KEY_PATH = '';        //商户的私钥（后缀是.pen）文件相对路径
    const PUBLIC_KEY_PATH = '';         //支付宝公钥（后缀是.pen）文件相对路径

    const SIGN_TYPE = 'MD5';            //签名方式 不需修改
    const INPUT_CHARSET = 'utf-8';      //字符编码格式 目前支持 gbk 或 utf-8
    const CACERT = '';                  //ca证书路径地址，用于curl中ssl校验 请保证cacert.pem文件在当前文件夹目录中
    const TRANSPORT  = 'http';          //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http

    public static function getConfigArray(){
        return array(
            'partner'   => self::PARTNER,
            'seller_id' => self::SELLER_ID,
            'private_key_path'  => self::PRIVATE_KEY_PATH,
            'ali_public_key_path'   => self::PUBLIC_KEY_PATH,
            'sign_type' => self::SIGN_TYPE,
            'input_charset' => self::INPUT_CHARSET,
            'cacert'        => self::CACERT,
            'transport'     => self::TRANSPORT,
        );
    }
}