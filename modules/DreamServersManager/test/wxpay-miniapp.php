<?php

// +----------------------------------------------------------------------
// | pay-php-sdk
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/pay-php-sdk
// +----------------------------------------------------------------------

//include   '../init.php';

// 加载配置参数
$config = [
    // 微信支付参数
    'wechat' => [
        // 沙箱模式
        'debug'      => false,
        // 应用ID
        'app_id'     => 'wx1eed012d4550cf4c',
        // 微信支付商户号
        'mch_id'     => '1520507531',
        /*
         // 子商户公众账号ID
         'sub_appid'  => '子商户公众账号ID，需要的时候填写',
         // 子商户号
         'sub_mch_id' => '子商户号，需要的时候填写',
        */
        // 微信支付密钥
        'mch_key'    => 'xmxtinydream2018XMXTINYDREAM2018',
        // 微信证书 cert 文件
        'ssl_cer'    => __DIR__ . '/cert/apiclient_cert.pem',
        // 微信证书 key 文件
        'ssl_key'    => __DIR__ . '/cert/apiclient_key.pem',
        // 缓存目录配置
        'cache_path' => '',
        // 支付成功通知地址
        'notify_url' => '',
        // 网页支付回跳地址
        'return_url' => '',
    ]
];
//$openid = $_REQUEST['uid'];
//$oid = $_REQUEST['oid'];
//$bill = $_REQUEST['bill'];
// 支付参数
$options = [
    'out_trade_no'     => $oid, // 订单号
    'total_fee'        => $bill, // 订单金额，**单位：分**
    'body'             => '小梦想互助-购买梦想', // 订单描述
    'spbill_create_ip' => $_SERVER["REMOTE_ADDR"], // 支付人的 IP
    'openid'           => $uid , // 支付人的 openID
    'notify_url'       => 'http://localhost/notify.php', // 定义通知URL
];

// 实例支付对象
$pay = new \Pay\Pay($config);

try {
    $result = $pay->driver('wechat')->gateway('miniapp')->apply($options);
    //echo '<pre>';
    //var_export($result);
    return $result;
} catch (Exception $e) {
    echo $e->getMessage();
}


