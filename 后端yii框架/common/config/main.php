<?php

Yii::$classMap['SendSMS'] = '@api/sms/SendSMS.php';
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        /* 'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ], */
        'cache' => [
            // 'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '127.0.0.1',
                'port' => 6379,
                'database' => 0,
            ],
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
    
            'transport' => [
                //这里如果你是qq的邮箱，可以参考qq客户端设置后再进行配置 http://service.mail.qq.com/cgi-bin/help?subtype=1&&id=28&&no=1001256
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                // qq邮箱
                'username' => 'no-reply@valid.maplegg.com',
                //授权码, 什么是授权码， http://service.mail.qq.com/cgi-bin/help?subtype=1&&id=28&&no=1001256
                'password' => 'gdnwdnnaovtfbgjc',
                'port' => '465',
                'encryption' => 'ssl',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['no-reply@valid.maplegg.com' => 'maplegg']
            ],
        ]
    ],
    
];
