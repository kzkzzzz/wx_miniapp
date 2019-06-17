<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Vcomments */

$this->title = '评论ID: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => '视频列表', 'url' => ['/videos/index']];
$this->params['breadcrumbs'][] = ['label' => '评论管理', 'url' => [Url::to(['/vcomments/index','vid'=>$model->vid]) ]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
.detail-view th,.detail-view td{
    text-align: left;
}
.detail-view th{
    width:10%;
}
</style>
<div class="vcomments-view">

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
            'user.username',
            'content',
            'created_at:datetime',

        ],
    ]) ?>

</div>
