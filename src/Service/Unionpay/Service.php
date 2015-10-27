<?php
namespace HuNanZai\HelpPay\Service\Unionpay;

use HuNanZai\HelpPay\Common\IThirdPartyPay;
use HuNanZai\HelpPay\Service\Unionpay\Lib\Common;
use HuNanZai\HelpPay\Service\Unionpay\Lib\Http;
use HuNanZai\HelpPay\Service\Unionpay\Lib\Secure;

class Service implements IThirdPartyPay
{
    /**
     * 发起支付
     *
     * @param      $trade_no //商户交易号（平台自己生成的时候确保唯一性）
     * @param      $amount //支付交易金额
     * @param null $extra //订单的其他交易信息
     *
     * @return mixed
     */
    public function pay($trade_no, $amount, $extra = null)
    {
        $parameters = array(
            'version'        => '5.0.0',
            'encoding'       => 'utf-8',
            'certId'         => Secure::getSignCertId(),
            'txnType'        => '01',                //交易类型
            'txnSubType'     => '01',                //交易子类
            'bizType'        => '000201',            //业务类型
            'frontUrl'       => Config::SDK_FRONT_NOTIFY_URL,        //前台通知地址
            'backUrl'        => Config::SDK_BACK_NOTIFY_URL,        //后台通知地址
            'signMethod'     => '01',        //签名方法
            'channelType'    => '08',        //渠道类型，07-PC，08-手机
            'accessType'     => '0',        //接入类型
            'merId'          => Config::MERCHANT_ID,                //商户代码，请改自己的测试商户号
            'orderId'        => $trade_no,    //商户订单号
            'txnTime'        => date('YmdHis'),    //订单发送时间
            'txnAmt'         => $amount * 100,        //交易金额，单位分
            'currencyCode'   => '156',    //交易币种
            'defaultPayType' => '0001',    //默认支付方式
            //'orderDesc' => '订单描述',  //订单描述，网关支付和wap支付暂时不起作用
            'reqReserved'    => ' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
        );

        Secure::sign($parameters);

        $result = Common::create_html($parameters, Config::SDK_FRONT_TRANS_URL);

        return $result;
    }

    /**
     * 验证回调(不同的第三方服务可能会有多个回调)
     *
     * @param null $type
     *
     * @return mixed
     */
    public function notify($type = null)
    {
        return Secure::verify($_POST) ? true : false;
    }

    /**
     * 查询一笔交易
     *
     * @param      $trade_no //商户交易号
     * @param      $thirdparty_trade_no //第三方的交易号
     *
     * @param null $extra
     *
     * @return mixed
     */
    public function search($trade_no, $thirdparty_trade_no, $extra = null)
    {
        $parameters = array(
            'version'     => '5.0.0',        //版本号
            'encoding'    => 'utf-8',        //编码方式
            'certId'      => Secure::getSignCertId(),    //证书ID
            'signMethod'  => '01',        //签名方法
            'txnType'     => '00',        //交易类型
            'txnSubType'  => '00',        //交易子类
            'bizType'     => '000000',        //业务类型
            'accessType'  => '0',        //接入类型
            'channelType' => '07',        //渠道类型
            'orderId'     => $thirdparty_trade_no,    //请修改被查询的交易的订单号
            'merId'       => Config::MERCHANT_ID,    //商户代码，请修改为自己的商户号
            'txnTime'     => $extra['send_time'],    //请修改被查询的交易的订单发送时间
        );
        Secure::sign($parameters);
        $result = Http::sendHttpRequest(Http::getRequestParamString($parameters), Config::SDK_SINGLE_QUERY_URL);

        $result_arr = Common::coverStringToArray($result);

        return Secure::verify($result_arr) ? $result_arr : false;
    }

    /**
     * 关闭交易
     *
     * @param      $trade_no //商户交易号
     * @param      $thirdparty_trade_no //第三方的交易号
     *
     * @param null $extra
     *
     * @return mixed
     */
    public function close($trade_no, $thirdparty_trade_no, $extra = null)
    {
        $parameters = array(
            'version'     => '5.0.0',        //版本号
            'encoding'    => 'utf-8',        //编码方式
            'certId'      => Secure::getSignCertId(),    //证书ID
            'signMethod'  => '01',        //签名方法
            'txnType'     => '31',        //交易类型
            'txnSubType'  => '00',        //交易子类
            'bizType'     => '000201',        //业务类型
            'accessType'  => '0',        //接入类型
            'channelType' => '07',        //渠道类型
            'orderId'     => $extra['close_no'],    //商户订单号，重新产生，不同于原消费
            'merId'       => Config::MERCHANT_ID,            //商户代码，请改成自己的测试商户号
            'origQryId'   => $thirdparty_trade_no,    //原消费的queryId，可以从查询接口或者通知接口中获取
            'txnTime'     => date('YmdHis'),    //订单发送时间，重新产生，不同于原消费
            'txnAmt'      => $extra['amount'] * 100,              //交易金额，消费撤销时需和原消费一致
            'backUrl'     => Config::SDK_BACK_NOTIFY_URL,       //后台通知地址
            'reqReserved' => ' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
        );

        Secure::sign($parameters);

        $result     = Http::sendHttpRequest(Http::getRequestParamString($parameters), Config::SDK_BACK_TRANS_URL);
        $result_arr = Common::coverStringToArray($result);

        return Secure::verify($result_arr) ? $result_arr : false;
    }

    /**
     * 交易退款
     *
     * @param      $batch_no //退款的批次号
     * @param      $trade_no //商户交易号
     * @param      $amount //退款的金额
     * @param null $extra //订单退款的其他信息
     *
     * @return mixed
     */
    public function refund($batch_no, $trade_no, $amount, $extra = null)
    {
        $parameters = array(
            'version'     => '5.0.0',        //版本号
            'encoding'    => 'utf-8',        //编码方式
            'certId'      => Secure::getSignCertId(),    //证书ID
            'signMethod'  => '01',        //签名方法
            'txnType'     => '04',        //交易类型
            'txnSubType'  => '00',        //交易子类
            'bizType'     => '000201',        //业务类型
            'accessType'  => '0',        //接入类型
            'channelType' => '07',        //渠道类型
            'orderId'     => $batch_no,    //商户订单号，重新产生，不同于原消费
            'merId'       => Config::MERCHANT_ID,    //商户代码，请修改为自己的商户号
            'origQryId'   => $extra['thirdparty_trade_no'],    //原消费的queryId，可以从查询接口或者通知接口中获取
            'txnTime'     => date('YmdHis'),    //订单发送时间，重新产生，不同于原消费
            'txnAmt'      => $extra['amount']*100,              //交易金额，退货总金额需要小于等于原消费
            'backUrl'     => Config::SDK_BACK_NOTIFY_URL,       //后台通知地址
            'reqReserved' => ' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
        );

        Secure::sign($parameters);

        $result = Http::sendHttpRequest( Http::getRequestParamString($parameters), Config::SDK_BACK_TRANS_URL);
        $result_arr = Common::coverStringToArray($result);

        return Secure::verify($result_arr) ? $result_arr : false;
    }
}