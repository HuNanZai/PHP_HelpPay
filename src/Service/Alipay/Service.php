<?php
namespace HuNanZai\HelpPay\Service\Alipay;

use HuNanZai\HelpPay\Common\IThirdPartyPay;
use HuNanZai\HelpPay\Service\Alipay\Lib\Notify;
use HuNanZai\HelpPay\Service\Alipay\Lib\Submit;

class Service implements IThirdPartyPay
{
    const NOTIFY_TYPE_PAY_RETURN = 'PayReturn';  //主动返回的回调类型
    const NOTIFY_TYPE_PAY_NOTIFY = 'PayNotify';  //服务器通知的回调类型
    const NOTIFY_TYPE_REFUND_NOTIFY = 'RefundNotify';   //退款的通知回调

    /**
     * 发起支付
     *
     * @param      $trade_no //交易号（平台自己生成的时候确保唯一性）
     * @param      $amount //支付交易金额
     * @param null $extra //订单的其他交易信息( title 订单标题, show_url 商品展示地址, body 订单描述等,有特殊需求的详见文档)
     *
     * @return mixed
     */
    public function pay($trade_no, $amount, $extra = null)
    {
        $parameter = array(
            'service'        => 'alipay.wap.create.direct.pay.by.user',
            'partner'        => trim(Config::PARTNER),
            'seller_id'      => trim(Config::SELLER_ID),
            'payment_type'   => '1',
            'notify_url'     => Config::NOTIFY_URL,
            'return_url'     => Config::RETURN_URL,
            'out_trade_no'   => $trade_no,
            'subject'        => $extra['title'] ? $extra['title'] : '支付宝交易订单',
            'total_fee'      => $amount,
            'show_url'       => $extra['show_url'] ? $extra['show_url'] : '',
            'body'           => $extra['body'] ? $extra['body'] : '',
            'it_b_pay'       => $extra['it_b_pay'] ? $extra['it_b_pay'] : '',
            'extern_token'   => $extra['extern_token'] ? $extra['extern_token'] : '',
            '_input_charset' => trim(Config::INPUT_CHARSET),
        );

        $submit = new Submit(Config::getConfigArray());

        return $submit->buildRequestForm($parameter, 'get', '确认');
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
        $method = "notify{$type}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        }

        return false;
    }

    /**
     * 支付的服务器回调验证调用函数
     *
     * @return array|bool
     */
    public function notifyPayNotify()
    {
        $notify = new Notify(Config::getConfigArray());
        $result = $notify->verifyNotify();
        if (!$result) {//验签失败 直接返回
            echo "fail";

            return false;
        }
        echo "success"; //验证成功就应该通知成功
        return array(
            'out_trade_no' => $_POST['out_trade_no'],
            'trade_no'     => $_POST['trade_no'],
            'trade_status' => $_POST['trade_status'],
        );
    }

    /**
     * 支付的直接返回的验证函数
     *
     * @return array|bool
     */
    public function notifyPayReturn()
    {
        $notify = new Notify(Config::getConfigArray());
        $result = $notify->verifyReturn();
        if (!$result) {//验签失败 直接返回
            echo "fail";

            return false;
        }
        echo "success"; //验证成功就应该通知成功
        return array(
            'out_trade_no' => $_GET['out_trade_no'],
            'trade_no'     => $_GET['trade_no'],
            'trade_status' => $_GET['trade_status'],
        );
    }

    /**
     * 验证退款的通知
     *
     * @return array
     */
    public function notifyRefundNotify()
    {
        $notify = new Notify(Config::getConfigArray());
        $result = $notify->verifyNotify();
        if (!$result) {
            echo "fail";
        }
        echo "success";

        return array(
            'batch_no'       => $_POST['batch_no'],
            'success_num'    => $_POST['success_num'],
            'result_details' => $_POST['result_details'],
        );
    }

    /**
     * 查询一笔交易(需要在支付宝后台申请对应的签约产品)
     *
     * @param $trade_no //商户交易号
     * @param $thirdparty_trade_no //第三方的交易号
     *
     * @return mixed
     */
    public function search($trade_no, $thirdparty_trade_no)
    {
        $parameter = array(
            'service'        => 'single_trade_query',
            'partner'        => trim(Config::PARTNER),
            'trade_no'       => $thirdparty_trade_no,
            'out_trade_no'   => $trade_no,
            '_input_charset' => trim(strtolower(Config::INPUT_CHARSET)),
        );
        $submit    = new Submit(Config::getConfigArray());
        $result    = $submit->buildRequestHttp($parameter);

        //@todo 对于查询到的xml进行解析
        $doc = new \DOMDocument();
        $doc->loadXML($result);
        if (empty($doc->getElementsByTagName("alipay")->item(0)->nodeValue)) {
            return false;
        }
    }

    /**
     * 关闭交易(需要在支付宝后台申请对应的签约产品)
     *
     * @param $trade_no //交易号
     * @param $thirdparty_trade_no //第三方的交易号
     *
     * @return mixed
     */
    public function close($trade_no, $thirdparty_trade_no)
    {
        $parameter = array(
            'service'        => 'close_trade',
            'partner'        => trim(Config::PARTNER),
            'trade_no'       => $thirdparty_trade_no,
            'out_trade_no'   => $trade_no,
            '_input_charset' => trim(strtolower(Config::INPUT_CHARSET)),
        );
        $submit    = new Submit(Config::getConfigArray());
        $result    = $submit->buildRequestHttp($parameter);

        //@todo 根据文档编写返回数据的处理逻辑
    }

    /**
     * 交易退款(支付宝其实可以批量退款， 这个可以自行实现)
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
        $parameter = array(
            'service'        => 'refund_fastpay_by_platform_pwd',
            'partner'        => trim(Config::PARTNER),
            'notify_url'     => Config::NOTIFY_URL,
            'seller_email'   => Config::SELLER_EMAIL,
            'refund_date'    => date('Y-m-d H:i:s'),
            'batch_no'       => $batch_no,
            'batch_num'      => 1,
            'detail_data'    => $extra['detail_data'] ? $extra['detail_data'] : "{$trade_no}^{$amount}^支付宝订单协商退款",
            '_input_charset' => trim(strtolower(Config::INPUT_CHARSET)),
        );
        $submit    = new Submit(Config::getConfigArray());
        $result    = $submit->buildRequestForm($parameter, "get", "确认");

        return $result;
    }

    /**
     * 支付宝的批量退款实现
     */
    public function refundMany()
    {
        //@todo: 支付宝支付批量退款接口，在此进行实现
    }
}