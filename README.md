#PHP_HelpPay [![Build Status](https://secure.travis-ci.org/HuNanZai/PHP_HelpPay.png?branch=master)](http://travis-ci.org/HuNanZai/PHP_HelpPay)

##Introduce
 - 目前主要应用于移动支付场景（支付宝的手机网站支付+银联支付手机网关支付+微信支付）
 - 众多支付"轮子"中的一个,但是不跟数据做任何瓜葛,只是想给后面碰到这类问题的人一个解决的方案
 - 第一次尝试开源,肯定有一些没有考虑周到的地方。欢迎大家提建议,一定会及时做出处理，一起进步!
 - 如果老板不反对&不违反公司策略&刚起步or实力不够，建议大家还是上成熟的第三方支付产品，别太累坏自己了

##Description
对于一个第三方的支付服务提供商，往往会有以下一些基本功能：

 - 发起一笔交易的支付
 - 查询一笔交易的状态
 - 关闭一笔交易
 - 对于一笔已经付款的交易退款

而由于涉及到实实在在资产的变动，所以往往又会有如下的需求：

 - 请求数据的签名加密等
 - 回调数据的验证等

但是由于各家的规范以及要求都不太一样，所以在具体的一些数据上会有差异，但大体上的行为就包括这些了~

 - 因为这些流程都差不多,其实我挺想把这些配置区分出来,这样可以在一个项目中支持不同的支付帐号收款和管理

##Usage
```
//发起支付
$res = HuNanZai\HelpPay\Api::pay(HuNanZai\HelpPay\Api::SERVICE_TYPE_ALIPAY, 'xxxxx', 100);
//查询交易
$res = HuNanZai\HelpPay\Api::search(HuNanZai\HelpPay\Api::SERVICE_TYPE_ALIPAY, 'xxxxx', 'xxxx');
//关闭交易
$res = HuNanZai\HelpPay\Api::close(HuNanZai\HelpPay\Api::SERVICE_TYPE_ALIPAY, 'xxxxx', 'xxxxx');
//交易退款
$res = HuNanZai\HelpPay\Api::refund(HuNanZai\HelpPay\Api::SERVICE_TYPE_ALIPAY, 'xxxxx', 'xxxxx', 1);
```

##Remark
1. 每家支付的处理方式都不一样，所以对于结果也需要不同的处理（暂时没有考虑封装返回接口以及请求数据）
2. log暂时也没有集成，有需要的可以自行添加