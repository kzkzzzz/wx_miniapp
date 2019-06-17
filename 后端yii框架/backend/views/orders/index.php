<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Goods;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p style="height:1px"></p>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ordernum',
            [
                'attribute'=>'username',
                'value'=>'user.username',
                'label'=>'用户名'
            ],
            [
                'attribute'=>'goods',
                'value'=>function($model){
                    $s = '';
                    $goodsArr = explode(',',$model->goods);
                    foreach ($goodsArr as $good) {
                       $goodEx = explode('-',$good);
                       $s .= Goods::findOne($goodEx[0])->gname.' x'.$goodEx[1].' ';
                    }
                    $gLen = strlen($s);
                    return mb_substr($s,0,40).($gLen>40?'...':'');
                },
                'format'=>'html'
            ],
            [
                'attribute'=>'total_price',
                'value'=>function($model){
                    return '￥'.$model->total_price;
                }
            ],
            [
                'attribute'=>'discount_price',
                'value'=>function($model){
                    return '￥'.$model->discount_price;
                },
                'contentOptions'=>['style'=>'color:red']
            ],
            
            'consignee',
            'phone',
            [
                'attribute'=>'address',
                'value'=>function($model){
                    $addLen = strlen($model->address);
                    return mb_substr($model->address,0,20).($addLen>20?'...':'');
                },
            ],
            'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'操作'
            ],
        ],
    ]); ?>


</div>
