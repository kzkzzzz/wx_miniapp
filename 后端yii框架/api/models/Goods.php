<?php

namespace api\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\caching\DbDependency;

/**
 * This is the model class for table "goods".
 *
 * @property int $id
 * @property int $cate_id
 * @property string $gname
 * @property string $gsale
 * @property string $gdesc
 * @property string $gprice 现价
 * @property string $disprice 原价
 * @property string $gimage
 */
class Goods extends \yii\db\ActiveRecord
{
    public $search;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['search'],'default','value'=>'']
            [['cate_id', 'gname', 'gdesc', 'gimage'], 'required'],
            [['cate_id'], 'integer'],
            [['gprice', 'disprice'], 'number'],
            [['gimage'], 'string'],
            [['gname', 'gsale'], 'string', 'max' => 191],
            [['gdesc'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cate_id' => 'Cate ID',
            'gname' => 'Gname',
            'gsale' => 'Gsale',
            'gdesc' => 'Gdesc',
            'gprice' => 'Gprice',
            'disprice' => 'Disprice',
            'gimage' => 'Gimage',
        ];
    }

    public function fields(){
        return array_merge(parent::fields(),[
            'gid'=>'id'
            ]);
    }

    /* public function extraFields()
    {
        return ['id'];
    }    */

    public function showData(){

        $dependency = new DbDependency([
            'sql' => 'select count(1) from goods'
        ]);

        /* $cacheRes = Yii::$app->cache->get('cates');

        if ($cacheRes == false) {
            $cates = Cates::find()->with('goods')->all();
            $cateArr = [];
            foreach ($cates as $cate) {
                $temp = [];
                $temp['catename'] = $cate->catename;
                $temp['goods'] = $cate->goods;
                $cateArr[] = $temp;
            }

            $response = [
                'category' => $cateArr,
                'shopinfo' => [
                    'image' => 'https://www.channelcc.cc/images/' . mt_rand(1, 24) . '.jpg',
                    'name' => \Faker\Factory::create()->name(),
                    'desc' => \Faker\Factory::create()->realText(70)
                ]
            ];

            Yii::$app->cache->set('cates', $response, 1800, $dependency);
            $cacheRes = Yii::$app->cache->get('cates');
        } */
        // Yii::$app->cache->delete('cates');

        $cates = Cates::find()->orderBy('odid desc,id asc')->with('goods')->all();
        $cateArr = [];
        foreach ($cates as $cate) {
            $temp = [];
            $temp['catename'] = $cate->catename;
            $temp['goods'] = $cate->goods;
            $cateArr[] = $temp;
        }

        $response = [
            'category' => $cateArr,
            'shopinfo' => [
                'image' => 'https://www.channelcc.cc/images/' . mt_rand(1, 24) . '.jpg',
                'name' => \Faker\Factory::create()->name(),
                'desc' => \Faker\Factory::create()->realText(70)
            ]
        ];

        return $response;
    }

    public function searchGoods($keyword){
        $query = static::find();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>[
                'pageSize'=>5,
                'validatePage'=>false
            ],
            'sort'=>[
                'defaultOrder'=>[
                    'id'=>SORT_ASC
                ]
            ]
        ]);
        $query->filterWhere(['like','CONCAT(gname,gdesc)',$keyword]);
        return ['result'=>$dataProvider->getModels(),'count'=>$dataProvider->getTotalCount()];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->gprice = floatval($this->gprice);
        $this->disprice = floatval($this->disprice);
    }



}
