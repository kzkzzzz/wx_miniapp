<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use Faker\Factory;
use api\models\Goods;
use api\models\Cates;
use api\models\Orders;

class GoodsController extends BaseController
{
    public $modelClass = 'api\models\Goods';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // $behaviors['authenticator']['optional'] = ['index','search','test'];
        unset($behaviors['authenticator']);
        return $behaviors;
    }


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {

        $goods = new $this->modelClass;
        return $goods->showData();
    }

    public function actionCheckserver()
    {
        return [
            'status' => 0,
            'time' => date('m-d H:i:s', time() + 3000),
            'ip' => Yii::$app->request->userIP,
            'payPlaceholder' => 'cdkey加商品金额'
        ];
    }

    public function actionPayment()
    {
        $bodyParams = Yii::$app->request->getBodyParams();
        $response = (new Orders())->createOrder($bodyParams);
        return $response;
    }
    public function actionSearch()
    {
        /* $data = 'a123456';
        $s = base64_encode(Yii::$app->security->encryptByKey($data, Yii::$app->params['tokenKey']));
        return ['a'=>Yii::$app->security->decryptByKey(base64_decode($s),Yii::$app->params['tokenKey'])]; */

        $goods = new $this->modelClass;
        $keyword = Yii::$app->request->getQueryParam('keyword');
        return $goods->searchGoods($keyword);
    }



    public function actionTest()
    {   
        $faker = Factory::create();
        //评论
        $userArr = [1,3,9,10,11,12,29,32];
        $begin = strtotime('last week');
        $end = strtotime('today');
        $timeArr = mt_rand($begin,$end);
        
        $data = [];
        for ($i=0; $i < 100; $i++) { 
            $arr = [];
            $arr[] = mt_rand(1,20);
            $arr[] = $userArr[array_rand($userArr,1)];
            $arr[] = $faker->realText(mt_rand(50,200));
            $arr[] = mt_rand($begin,$end);
            $arr[] = time();
            $data[] = $arr;
        }
        $ds = Yii::$app->db->createCommand()->batchInsert('vcomments',['vid','uid','content','created_at','updated_at'],$data)->execute();
        return $ds;

        

        // 商品
        /* $rangArr = range(21, 93);
        shuffle($rangArr);
        foreach ($rangArr as $value) {
            $price = mt_rand(50, 200);
            $arr = [];
            // $arr[] = 2;
            $arr[] = mt_rand(3, 12);
            $arr[] = $faker->lastName;
            $arr[] = 0;
            $arr[] = $faker->realText(200);
            // $arr[] = $faker->randomFloat($nbMaxDecimals = 1, $min = 50, $max = 80); 
            $arr[] = $price;
            // $arr[] = mt_rand(100,130);
            $arr[] = $price;
            $arr[] = "http://www.channelcc.cc/images/goods/g ({$value}).jpg";
            $data[] = $arr;
        } */

        // 视频
        /* for ($i=0; $i < 75; $i++) { 
            $arr = [];
            $arr[] = mt_rand(1,12);
            $arr[] = $faker->lastName;
            $arr[] = 0;
            $arr[] = $faker->realText(200);
            $arr[] = $faker->randomFloat($nbMaxDecimals = 1, $min = 50, $max = 80); 
            $arr[] = mt_rand(100,130);
            $arr[] = "https://www.channelcc.cc/images/".mt_rand(1,30).".jpg";
            $data[] = $arr;
        }
        // return ['s'=>$data];

        $ds = Yii::$app->db->createCommand()->batchInsert('goods', ['cate_id', 'gname', 'gsale', 'gdesc', 'gprice', 'disprice', 'gimage'], $data)->execute();
        return ['s' => $ds]; */

        // 分类
        /* for ($i=0; $i < 12; $i++) { 
            $arr = [];
            $arr[] = $faker->firstName();
            $data [] = $arr;
        }
        $ds = Yii::$app->db->createCommand()->batchInsert('cates',['catename'],$data)->execute();
        return ['s'=>$ds]; */
        // return ['s'=>$data];
    }
}
