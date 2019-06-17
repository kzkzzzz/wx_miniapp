<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'timezone'=>'Asia/Shanghai',
    'language'=>'zh-CN',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers'=>[
                'application/json' => 'yii\web\JsonParser',                 
            ]
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => true,
            // 'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'enableSession' => false
        ],
        /* 'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ], */
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'response' => [
            'format'=> yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function($event){ 
                $response = $event->sender;
                // if($response->data != null && empty(Yii::$app->request->get('error'))){}
                if(empty(Yii::$app->request->get('error'))){
                        $response->data = [
                            'code'=>$response->statusCode,
                            'errmsg'=>$response->statusText,
                            'body'=>$response->data
                        ];
                        $response->statusCode = 200;
                }
            }
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'GET weather'=>'v1/default/weather',
                'GET banner'=>'v1/default/banner',

                'POST login'=>'login/index',
                'POST regsend'=>'login/send',
                'POST reg'=>'login/register',
                'POST <controller:login>/<action:(openid|unionid)>'=>'<controller>/<action>',
                'GET <controller:login>/<action:(sendcode|sff)>'=>'<controller>/<action>',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/goods',
                    'extraPatterns' => [
                        'GET search' => 'search',
                        'GET test' => 'test',
                        'GET checkserver' => 'checkserver',
                        'PUT payment'=>'payment'
                    ],
                    // 'pluralize'=>false
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user',
                    'extraPatterns' => [
                        'GET order' => 'order',
                        'GET check' => 'check',
                    ],
                    'pluralize'=>false
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/videos',
                    'extraPatterns' => [
                        'GET <action:(search|like|recommend|vcomment)>'=>'<action>',
                        'PUT savecomment'=>'savecomment',
                        'HEAD recordplay'=>'recordplay'
                    ],
                ],
                /* [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'login',
                    
                    'pluralize'=>false
                ] */
            ],
        ],

    ],
    'params' => $params,
];
