<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Goods;

/* @var $this yii\web\View */
/* @var $model backend\models\Orders */

$this->title = '订单号：'.$model->ordernum;
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="orders-view">

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
        'attributes' => [
            'id',
            'ordernum',
            [
                'attribute'=>'user.username',
                'label'=>'用户名'
            ],
            [
                'attribute'=>'goods',
                'value'=>function($model){
                    $s = '';
                    $goodsArr = explode(',',$model->goods);
                    foreach ($goodsArr as $good) {
                       $goodEx = explode('-',$good);
                       $s .= Goods::findOne($goodEx[0])->gname.' x'.$goodEx[1].'&nbsp;&nbsp;&nbsp;';
                    }
                    return $s;
                },
                'format'=>'html'
            ],
            'goods',
            'total_price',
            'discount_price',
            'consignee',
            'phone',
            'address',
            'created_at',
        ],
    ]) ?>

</div>
