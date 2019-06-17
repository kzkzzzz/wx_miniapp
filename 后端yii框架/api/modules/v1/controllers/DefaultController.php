<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use linslin\yii2\curl;

/**
 * Default controller for the `v1` module
 */
class DefaultController extends Controller
{
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        // return $this->render('index');
        return ['s'=>'index'];
    }

    public function actionWeather(){
        $city = preg_replace('/(市|县|区)/','',Yii::$app->request->get('city'));
        $curl = new curl\Curl();
        $getParams=['version'=>'v6'];
        if($city){
            $getParams['city'] = $city;
        } else{
            $getParams['ip'] = Yii::$app->request->userIP;
        }
        $curl->reset()
             ->setOptions([CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST, false])
             ->setGetParams($getParams)
             ->get('https://www.tianqiapi.com/api/');
        $response = json_decode($curl->response,true);
        $response['tem'] = $response['tem'].'℃';
        
        $response['wea_img'] = "http://www.channelcc.cc/images/weather/{$response['wea_img']}.png";
        $response['air'] ='空气质量: '.$response['air'];
        $response['humidity'] ='相对湿度: '.$response['humidity'];
        return $response;
    }

    public function actionBanner(){
        /* $mp4 = dir(getcwd().'/videos');
        $arr = [];
        while(($file = $mp4->read()) != false){
            $temp = [];
            $temp[]=str_replace('.mp4','',$file);
            $temp[]=str_replace('#','%23','http://yii.maplegg.com/videos/'.$file);
            $temp[]=str_replace('#','%23','http://yii.maplegg.com/images/'.$file.'.jpg');
            $temp[]=0;
            $temp[]=0;
            $arr[] = $temp;
        }
        $arr = array_slice($arr,2);

        $ds = Yii::$app->db->createCommand()->batchInsert('videos',['title','play_url','img_url','thumbs','play_num'],$arr)->execute();
        return $ds; */
        $banner = [];
        $rand =  array_rand(range(1,68),5);

        foreach ($rand as $value) {
            $banner[] = "http://www.channelcc.cc/images/banner/b (".($value+1).").jpg";
        }

        return $banner;
    }
}
