<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */

$this->title = '查看商品: ' . $model->gname;;
$this->params['breadcrumbs'][] = ['label' => '商品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
.set-width th{
    width: 10%;
}
.gimage{
    max-width: 5vw;
    max-height: 4vw;
}
</style>
<div class="goods-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'options'=>['class'=>'table table-striped table-bordered detail-view set-width'],
        'attributes' => [
            'id',
            // 'cate_id',
            [
                'label'=>'商品分类',
                'attribute'=>'cates.catename'
            ],
            'gname',
            'gsale',
            [
                'attribute'=>'gprice',
                'contentOptions'=>['style'=>'color:red'],
                'value'=>function($model){
                    return '￥'.$model->gprice;
                },
            ],
            [
                'attribute'=>'disprice',
                'value'=>function($model){
                    return '￥'.$model->disprice;
                },
            ],
            [
                'attribute'=>'gimage',
                'value'=>function($model){
                    return $model->htmlimg;
                },
                'format'=>'html'
                // 'visible'=>false
            ],
            'gdesc',
        ],
    ]) ?>

</div>

