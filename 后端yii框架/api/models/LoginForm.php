<?php
namespace api\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $email;
    public $phone;
    public $account;

    public function rules(){
        return [
            [['username','account','email','phone'],'trim'],
            
            [['username','password','email'],'required','message'=>"{attribute}不能为空",'on'=>['mail']],
            ['email','email','message'=>'E-mail 邮箱格式不正确','on'=>['mail']],
            ['email','unique','targetClass'=>'\api\models\User','on'=>['mail'],'message'=>'此邮箱已被注册！'],
            
            [['username','password','phone'],'required','message'=>"{attribute}不能为空",'on'=>['sms']],
            ['phone','checkPhone','on'=>['sms']],
            ['phone','unique','targetClass'=>'\api\models\User','on'=>['sms'],'message'=>'此手机号已被注册！'],

            [['account','password'],'required','message'=>"{attribute}不能为空",'on'=>['login']],
            ['account','string','min'=>6,'tooShort'=>"账号长度至少6位",'on'=>['login']],

            // [ 'verifyCode', 'captcha', 'captchaAction' => '/login/captcha','on'=>['login']],

            ['password','checkPassword','on'=>['login']],
        ];
    }


    public function checkPhone($attribute){
        $pattern = '/^1([38][0-9]|4[579]|5[0-35-9]|6[56]|7[0135678]|9[89])\d{8}$/';
        if(!preg_match($pattern,$this->phone)){
            $this->addError($attribute,'手机号码格式不正确');
        }
    }

    public function checkPassword($attribute){
        $user = User::findByAccount($this->account);
        if(!$user || !$user->validatePassword($this->password)){
            $this->addError($attribute,'账号或者密码不正确');
        }
    }

    public function login($opendata,$userinfo,$login_params){

        // 微信授权 通过openid登录
        if($opendata['openid']){
            $user = User::findByOpenid($opendata['openid']);
            if(!$user){
                $user = new User();
                $user->username = $userinfo['nickName'];
                $user->profile_url = $userinfo['avatarUrl'];
                $user->openid = $opendata['openid'];
                $user->session_key = $opendata['session_key'];
                $user->status = User::STATUS_ACTIVE;
                $user->access_token = str_shuffle($opendata['session_key']);
                $res = $user->save(false);
                return [
                    'success'=>$res,
                    'login_time'=>date('Y-m-d H:i:s',$user->updated_at),
                    'token'=>User::encryToken($user->access_token),
                    'userInfo'=>array_merge($userinfo,['id'=>$user->id])
                ];
            } else{
                $user->profile_url = $userinfo['avatarUrl'];
                $user->session_key = $opendata['session_key'];
                $user->access_token = str_shuffle($opendata['session_key']);
                $res = $user->save(false);
                return [
                    'success'=>$res,
                    'login_time'=>date('Y-m-d H:i:s',$user->updated_at),
                    'token'=>User::encryToken($user->access_token),
                    'userInfo'=>array_merge($userinfo,['id'=>$user->id])
                ];
            }
        } else{
            $this->scenario = 'login';
            $this->attributes = $login_params;
            $user = User::findByAccount($this->account);
            
            // return ['a'=>$this->getAttributes()];
            if($this->validate()){
                $token = $user->generateAccessToken();
                $res = $user->save();
                return [
                    'success'=>$res,
                    'login_time'=>date('Y-m-d H:i:s',$user->updated_at),
                    'token'=>User::encryToken($token),
                    'userInfo'=>User::findByAccount($this->account)
                ];
            } else{
                return $this->errors;
            }
        }

    }

    public function register($params=[]){
        $user = new User();
        $this->username = $params['username'];
        $this->password = $params['password'];

        switch ($params['method']) {
            case 'email':
                $this->email = $params['account'];
                $this->scenario = 'mail';

                if (!$this->validate()) {
                    $errors = array_merge(['errors'=>$this->errors],['status'=>2]);
                    return $errors;
                }
                $user->email = $params['account']; 
                $user->phone = 'string_'.str_shuffle($user->email);  //随机填充一个号码
                break;

            case 'sms':
                $this->phone = $params['account'];
                $this->scenario = 'sms';

                if (!$this->validate()) {
                    $errors = array_merge(['errors'=>$this->errors],['status'=>2]);
                    return $errors;
                }
                $user->phone = $params['account'];
                $user->email = 'string_'.str_shuffle($user->phone); //随机填充一个号码
                break;
        }

        //从缓存取出验证码进行验证
        $param_code = (int)$params['valid_code'];
        $cache = Yii::$app->cache->get($params['account']);
        // return ['a'=>$cache];

        if(empty($cache['valid_code'])){
            return ['status'=>1,'valid_code'=>'请先获取验证码'];
        }
        if($param_code !== $cache['valid_code']){
            return ['status'=>1,'valid_code'=>'验证码不正确'];
        }

        $user->profile_url = 'https://www.maplegg.com/image/'.mt_rand(1,25).'.jpg';//随机一个头像
        $user->openid = 'string_openid'.str_shuffle('abcdEFGxyZ'.time());//随便随机一个openid
        $user->username = $params['username'];
        $user->setPassword($params['password']);
        $user->status = User::STATUS_ACTIVE;
        $token = $user->generateAccessToken();
        $res = $user->save();

        $clear = Yii::$app->cache->delete($params['account']); //注册成功清除验证码缓存
        return [
            'user_id'=>$user->id,
            'success'=>$res,
            'login_time'=>date('Y-m-d H:i:s',$user->updated_at),
            'token'=> User::encryToken($token),
            'msg'=>'注册成功！',
            'status'=>0,
            'userInfo'=>User::findIdentity($user->id)
        ];
    }


}