<?php
namespace HuNanZai\HelpPay\Service\Alipay;

use HuNanZai\HelpPay\Common\Func;
use HuNanZai\HelpPay\Common\IThirdPartyPay;

class Service implements IThirdPartyPay
{
    /**
     * 发起支付
     *
     * @param      $trade_no //交易号（平台自己生成的时候确保唯一性）
     * @param      $amount //支付交易金额
     * @param null $extra //订单的其他交易信息
     *
     * @return mixed
    */
    public function pay($trade_no, $amount, $extra = null)
    {
    }

    /**
     * 验证回调(不同的第三方服务可能会有多个回调)
     *
     * @return mixed
     */
    public function verify()
    {
        // TODO: Implement verify() method.
    }

    /**
     * 查询一笔交易
     *
     * @param $trade_no //交易号
     *
     * @return mixed
     */
    public function search($trade_no)
    {
        // TODO: Implement search() method.
    }

    /**
     * 关闭交易
     *
     * @param $trade_no //交易号
     *
     * @return mixed
     */
    public function close($trade_no)
    {
        // TODO: Implement close() method.
    }

    /**
     * 交易退款
     *
     * @param $trade_no //交易号
     * @param $amount //退款的金额
     *
     * @return mixed
     */
    public function refund($trade_no, $amount)
    {
        // TODO: Implement refund() method.
    }
}