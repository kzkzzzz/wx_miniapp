<?php

class SendSMS
{
    private $url = 'https://yun.tim.qq.com/v5/tlssmssvr/sendsms';
    private $appSdkId;
    private $appKey;
    private $tel;
    private $tpl_id;

    public function __construct($appSdkId,$appKey,$tel,$tpl_id){
        $this->appSdkId = $appSdkId;
        $this->appKey = $appKey;
        $this->tel = $tel;
        $this->tpl_id = $tpl_id;
        
    }

    public function curlPost($url,$params){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-type:applicaion/json']);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function makeStrRand(){
        $string = 'qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM';
        return substr(str_shuffle($string),mt_rand(0,strlen($string)-12),12);
    }

    public function sendSMS($valid_code){
        $time = time();
        $tel = $this->tel;
        $strRand = $this->makeStrRand();
        $appKey = $this->appKey;
        $sigStr = "appkey={$appKey}&random={$strRand}&time={$time}&mobile={$tel}";
        $sig = hash('sha256',$sigStr);

        $postBody = [
            'params'=>[$valid_code,5],
            'sig'=>$sig,
            'sign'=>'MAPLEGG',
            'tel'=>[
                'mobile'=>$tel,
                'nationcode'=>'86'
            ],
            'time'=>$time,
            'tpl_id'=>$this->tpl_id
        ];

        $url = "{$this->url}?sdkappid={$this->appSdkId}&random={$strRand}";
        return json_decode($this->curlPost($url,json_encode($postBody)));
        
    }
}