<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Orders */

$this->title = '订单号：'.$model->ordernum;;
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '查看订单', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改收货信息';

?>
<div class="orders-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
