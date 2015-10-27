<?php
namespace HuNanZai\HelpPay\Service\Unionpay\Lib;

use HuNanZai\HelpPay\Service\Unionpay\Config;

class PublicEncrypt
{
    public static function EncryptedPin($sPin, $sCardNo, $sPubKeyURL)
    {
        //global $log;
        $sPubKeyURL = trim(Config::SDK_ENCRYPT_CERT_PATH, " ");
        //	$log->LogInfo("DisSpaces : " . PubKeyURL);
        $fp = fopen($sPubKeyURL, "r");
        if ($fp != null) {
            $sCrt = fread($fp, 8192);
            //		$log->LogInfo("fread PubKeyURL : " . $sCrt);
            fclose($fp);
        }
        $sPubCrt = openssl_x509_read($sCrt);
        if ($sPubCrt === false) {
            print("openssl_x509_read in false!");

            return (-1);
        }
        //	$log->LogInfo($sPubCrt);
        //	$sPubKeyId = openssl_x509_parse($sCrt);
        //	$log->LogInfo($sPubKeyId);
        $sPubKey = openssl_x509_parse($sPubCrt);
        //	$log->LogInfo($sPubKey);
        //	openssl_x509_free($sPubCrt);
        //	print_r(openssl_get_publickey($sCrt));

        $sInput = PinBlock::Pin2PinBlockWithCardNO($sPin, $sCardNo);
        if ($sInput == 1) {
            print("Pin2PinBlockWithCardNO Error ! : " . $sInput);

            return (1);
        }
        $iRet = openssl_public_encrypt($sInput, $sOutData, $sCrt, OPENSSL_PKCS1_PADDING);
        if ($iRet === true) {
            //		$log->LogInfo($sOutData);
            $sBase64EncodeOutData = base64_encode($sOutData);

            //print("PayerPin : " . $sBase64EncodeOutData);
            return $sBase64EncodeOutData;
        }else {
            print("openssl_public_encrypt Error !");

            return (-1);
        }
    }
}