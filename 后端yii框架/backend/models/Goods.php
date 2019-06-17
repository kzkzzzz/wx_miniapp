<?php

namespace backend\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "goods".
 *
 * @property int $id
 * @property int $cate_id
 * @property string $gname
 * @property int $gsale
 * @property string $gdesc
 * @property string $gprice 现价
 * @property string $disprice 原价
 * @property string $gimage
 */
class Goods extends \yii\db\ActiveRecord
{

    public $good_image;
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
            [['cate_id', 'gprice','disprice','gname','gimage'], 'required'],
            [['cate_id', 'gsale'], 'integer'],
            [['gsale'],'default','value'=>0],
            [['gprice', 'disprice'], 'number'],
            [['gname'], 'string', 'max' => 100],
            [['gdesc', 'gimage'], 'string', 'max' => 255],

            [['good_image'],'file', 'skipOnEmpty' => false, 'extensions' => 'png,jpg,jpeg','on'=>['upload']]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['upload'] = ['good_image'];   
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '商品ID',
            'cate_id' => 'Cate_ID',
            'gname' => '商品名称',
            'gsale' => '商品销量',
            'gdesc' => '商品描述',
            'gprice' => '商品现价',
            'disprice' => '商品原价',
            'gimage' => '商品图片',
        ];
    }

    public function getCates(){
        return $this->hasOne(Cates::className(),['id'=>'cate_id']);
    }

    public function getHtmlimg(){
        $img = Html::img($this->gimage,['class'=>'gimage']);
        return $img;
    }
}
