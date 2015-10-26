<?php
namespace HuNanZai\HelpPay\Common;
/**
 * 第三方支付应该遵循的规范
 *
 * Interface IThirdPartyPay
 *
 */
Interface IThirdPartyPay
{
    /**
     * 发起支付
     *
     * @param      $trade_no    //商户交易号（平台自己生成的时候确保唯一性）
     * @param      $amount      //支付交易金额
     * @param null $extra       //订单的其他交易信息
     *
     * @return mixed
     */
    public function pay($trade_no, $amount, $extra=null);

    /**
     * 验证回调(不同的第三方服务可能会有多个回调)
     *
     * @param null $type
     *
     * @return mixed
     */
    public function notify($type=null);

    /**
     * 查询一笔交易
     *
     * @param $trade_no             //商户交易号
     * @param $thirdparty_trade_no  //第三方的交易号
     *
     * @return mixed
     */
    public function search($trade_no, $thirdparty_trade_no);

    /**
     * 关闭交易
     *
     * @param $trade_no     //商户交易号
     * @param $thirdparty_trade_no //第三方的交易号
     *
     * @return mixed
     */
    public function close($trade_no, $thirdparty_trade_no);

    /**
     * 交易退款
     *
     * @param $batch_no     //退款的批次号
     * @param $trade_no     //商户交易号
     * @param $amount       //退款的金额
     * @param null $extra   //订单退款的其他信息
     *
     * @return mixed
     */
    public function refund($batch_no, $trade_no, $amount, $extra=null);
}
