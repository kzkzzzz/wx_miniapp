<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>

.grid-view .gimage{
    max-width: 5vw;
    max-height: 4.5vw;
}
</style>
<div class="goods-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('添加商品', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'captionOptions'=>['width'=>'10%'],
        'columns' => [
            [
                'attribute'=>'id',
                'contentOptions'=>['width'=>'8%']
            ],
            [
                'attribute'=>'gimage',
                'contentOptions'=>['width'=>'10%'],
                'format'=>'html',
                'value'=>'htmlimg'
            ],
            [
                'attribute'=>'catename',
                'value'=>'cates.catename',
                'label'=>'所属分类',
                'contentOptions'=>['width'=>'10%']
            ],
            [
                'attribute'=>'gname',
                'contentOptions'=>['width'=>'10%']
            ],
            [
                'attribute'=>'gsale',
                'contentOptions'=>['width'=>'8%'],
                'value'=>function($model){
                    return $model->gsale.' 件';
                },
            ],
            [
                'attribute'=>'gprice',
                'contentOptions'=>['width'=>'10%','style'=>'color:red'],
                'value'=>function($model){
                    return '￥'.$model->gprice;
                },
            ],
            [
                'attribute'=>'disprice',
                'value'=>function($model){
                    return '￥'.$model->disprice;
                },
                'contentOptions'=>['width'=>'10%'],
            ],
            [
                'attribute'=>'gdesc',
                'contentOptions'=>['width'=>'20%'],
                'value'=>function($model){
                    $gdescLen = strlen($model->gdesc);
                    return mb_substr($model->gdesc,0,30).($gdescLen>30?'...':'');
                }
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'商品操作',
                'contentOptions'=>['width'=>'10%']
            ],
        ],
    ]); ?>


</div>
