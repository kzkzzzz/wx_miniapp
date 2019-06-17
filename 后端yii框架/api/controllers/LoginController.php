<?php
namespace api\controllers;

use Yii;
use SendSMS;
use yii\web\Controller;
use api\models\LoginForm;
use \Faker\Factory;
use linslin\yii2\curl;
use yii\helpers\Url;
use api\models\User;
use api\models\Vcomments;

class LoginController extends Controller
{
    public $enableCsrfValidation = false;
    public $opendata;
    public $userinfo;


    public function actionIndex()
    {
        /* $s = User::find()->where(['email'=>'ffffff'])->orWhere(['phone'=>'fffffffff'])->one();
        return ['s'=>$s]; */
        // return $this->generateVerifyCode();

        if ($code = Yii::$app->request->post('code')) {
            $curl = new curl\Curl();
            $curl->reset()
                ->setOptions([CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST, false])
                ->setGetParams(
                    [
                        'appid' => 'appid',
                        'secret' => 'secret',
                        'js_code' => $code,
                        'grant_type' => 'authorization_code'
                    ]
                )
                ->get('https://api.weixin.qq.com/sns/jscode2session');
            $this->opendata = json_decode($curl->response, true);
        }

        $this->userinfo = Yii::$app->request->post('userinfo');
        $model = new LoginForm();
        $login_params = Yii::$app->request->bodyParams;

        if ($res = $model->login($this->opendata, $this->userinfo,$login_params)) {
            return $res;
        }
    }

    public function actionOpenid()
    {
        /* sleep(4);
        return ['a'=>'openid']; */
        $code = Yii::$app->request->post('code');
        $curl = new curl\Curl();
        $curl->reset()
            ->setOptions([CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST, false])
            ->setGetParams(
                [
                    'appid' => 'appid',
                    'secret' => 'secret',
                    'js_code' => $code,
                    'grant_type' => 'authorization_code'
                ]
            )
            ->get('https://api.weixin.qq.com/sns/jscode2session');

        return json_decode($curl->response);
        
    }

    public function actionSend()
    {
        /* $m = '345263950@qq.com';
        Yii::$app->cache->set($m,'hahaha',60);
        return ['a'=>Yii::$app->cache->get($m)]; */

        $send_msg = Yii::$app->request->getBodyParams();

        if (count($send_msg) != 3) {
            return ['status' => 1, 'msg' => '参数不正确'];
        } elseif (!in_array($send_msg['method'], ['email', 'sms'])) {
            return ['status' => 1, 'msg' => '方法只支持email和sms'];
        }


        switch ($send_msg['method']) {
            case 'email':
                $email_pattern = '/^[a-zA-Z0-9]+([-_a-zA-Z0-9])*@([a-zA-Z0-9]+([-_a-zA-z0-9])*\.)+[a-z]{2,}$/';
                if (!preg_match($email_pattern,$send_msg['account'])) {
                    return ['status' => 2, 'msg' => 'E-mail 邮箱格式不正确'];
                }
                break;

            case 'sms':
                $phone_pattern = '/^1([38][0-9]|4[579]|5[0-35-9]|6[56]|7[0135678]|9[89])\d{8}$/';
                if (!preg_match($phone_pattern,$send_msg['account'])) {
                    return ['status' => 2, 'msg' => '手机号码格式不正确'];
                }
                return ['status'=>999,'msg'=>'短信注册暂时不可用'];
                break;
        }
        $cache = Yii::$app->cache->get($send_msg['account']);

        if($cache){
            if($send_msg['time'] < $cache['time']){
                return ['status'=>'1','msg'=>'一分钟内只能发送一次'];
            }
        }

        $path = Url::to(['/login/sendcode', 'method' => $send_msg['method'], 'account' => $send_msg['account']]);
        static::asyncSend($path);
        return ['status'=>'0','msg'=>'验证码发送成功','sendTo'=>$send_msg['account']];
    }


    /**
     * 异步调用发送，GET参数由asyncSend的fsockopen方法传来
     */
    public function actionSendcode()
    {
        //GET 参数
        $params = Yii::$app->request->getQueryParams();
        $account = $params['account'];

        $cache = Yii::$app->cache;
        $cache_array = [
            'valid_code' => mt_rand(100000,999999),
            'time'=>time()+60,
        ];
        $cache->set($account,$cache_array,300);

        switch ($params['method']) {
            case 'email':
                $subject = 'MAPLEGG注册验证码（请勿回复）';
                $body = "<div>
                <p style='font-size: 1.5rem'>你好，请在5分钟内完成验证</p>
                <p style='font-size: 1.2rem'><span>验证码为：</span><span  style='color:#013ADF;font-weight:600'>{$cache_array['valid_code']}</span></p>
                <span style='background-color: #000000;height: 1px;display: inline-block;width: 205px'></span>
                <p>By 测试一下GGGGGGG</p>
                </div>";
                $mail = Yii::$app->mailer->compose();
                $mail->setTo($account);
                $mail->setSubject($subject);
                $mail->setHtmlBody($body);
                $mail->send();
                break;
            
            case 'sms':
                $sms = new SendSMS('app_id','secret',$account,'tem_id');
                $sms->sendSMS($cache_array['valid_code']);
                break;
        }


    }

    public function actionRegister(){

        $model = new LoginForm();
        $params = Yii::$app->request->getBodyParams();
 
        return $model->register($params);
    }

    /**
     * 异步发送邮件和短信
     */
    public static function asyncSend($config)
    {
        $fp = fsockopen('yii.maplegg.com', 80, $errno, $errstr, 30);
        if (!$fp) {
            return ['errCode' => $errno, 'errStr' => $errstr];
        } else {
            $http = "GET {$config} HTTP/1.1\r\n";
            $http .= "Host: yii.maplegg.com\r\n";
            $http .= "Connection: Close\r\n\r\n";
            fwrite($fp, $http);
            fclose($fp);
        }
    }

    /* public function actionSff(){
        $v = Vcomments::findOne(1);
        return $v;
    } */

    /* public function actionUnionid(){
        $params = Yii::$app->request->post();
        $session_key = $params['session_key'];
        $iv = $params['iv'];
        $encryptedData = $params['encryptedData'];
        $baseKey = base64_decode($session_key);
        $baseIv= base64_decode($iv);
        $baseEnt = base64_decode($encryptedData);
        $result = openssl_decrypt($baseEnt,'AES-128-CBC',$baseKey,OPENSSL_RAW_DATA,$baseIv);
        return ['a'=>json_decode($result,true)];
    } */
}
