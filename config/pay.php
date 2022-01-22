<?php

return [

    'wechat' => [
        'appid' => 'wxe011d49d77124e2b', // APP APPID
        'app_id' => 'wxe011d49d77124e2b', // 公众号 APPID
        'miniapp_id' => 'wxe011d49d77124e2b', // 小程序 APPID
        'mch_id' => '1494847022',
        'key' => 'bb908bd5d209af487eee6f095f90d1df',
        'notify_url' => 'https://api.print.com/api/common/notify/wechat',
        'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
        'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
        'log' => [ // optional
            'file' => './logs/wechat.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'normal', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ],

    'alipay' => [
        'app_id' => '',
        'notify_url' => '',
        'return_url' => '',
        'ali_public_key' => '',
        // // 加密方式： **RSA2**
        'private_key' => '',
        'app_cert_public_key' => '', //应用公钥证书路径
        'alipay_root_cert' => '', //支付宝根证书路径
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'normal', // optional,设置此参数，将进入沙箱模式
    ]
];
