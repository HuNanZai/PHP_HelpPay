<?php
namespace HuNanZai\HelpPay;

use HuNanZai\HelpPay\Common\IThirdPartyPay;

class Api
{
    const SERVICE_TYPE_ALIPAY = 'Alipay';
    const SERVICE_TYPE_UNIONPAY = 'Unionpay';

    private static $services = null;

    /**
     * @param $type
     *
     * @return IThirdPartyPay
     * @throws \Exception
     */
    private static function getServiceByType($type){
        if( !in_array($type, array(self::SERVICE_TYPE_ALIPAY, self::SERVICE_TYPE_UNIONPAY)) ){
            throw new \Exception('Unsupported service type: '.$type);
        }

        if( !self::$services[$type] ){
            $class_name = "Service\\{$type}\\Service";
            self::$services[$type] = new $class_name();
        }

        return self::$services[$type];
    }

    /**
     * 发起支付
     *
     * @param      $type
     * @param      $trade_no //商户交易号（平台自己生成的时候确保唯一性）
     * @param      $amount //支付交易金额
     * @param null $extra //订单的其他交易信息
     *
     * @return mixed
     * @throws \Exception
     */
    public static function pay($type, $trade_no, $amount, $extra = null)
    {
        return self::getServiceByType($type)->pay($trade_no, $amount, $extra);
    }

    /**
     * 验证回调(不同的第三方服务可能会有多个回调)
     *
     * @param      $type
     * @param null $notify_type
     *
     * @return mixed
     * @throws \Exception
     */
    public static function notify($type, $notify_type=null)
    {
        return self::getServiceByType($type)->notify($notify_type);
    }

    /**
     * 查询一笔交易
     *
     * @param $type
     * @param $trade_no //商户交易号
     * @param $thirdparty_trade_no //第三方的交易号
     *
     * @return mixed
     */
    public static function search($type, $trade_no, $thirdparty_trade_no)
    {
        return self::getServiceByType($type)->search($trade_no, $thirdparty_trade_no);
    }

    /**
     * 关闭交易
     *
     * @param $type
     * @param $trade_no //商户交易号
     * @param $thirdparty_trade_no //第三方的交易号
     *
     * @return mixed
     */
    public static function close($type, $trade_no, $thirdparty_trade_no)
    {
        return self::getServiceByType($type)->close($trade_no, $thirdparty_trade_no);
    }

    /**
     * 交易退款
     *
     * @param      $type
     * @param      $batch_no //退款的批次号
     * @param      $trade_no //商户交易号
     * @param      $amount //退款的金额
     * @param null $extra //订单退款的其他信息
     *
     * @return mixed
     */
    public static function refund($type, $batch_no, $trade_no, $amount, $extra = null)
    {
        return self::getServiceByType($type)->refund($batch_no, $trade_no, $amount, $extra);
    }
}