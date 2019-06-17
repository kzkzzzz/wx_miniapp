<?php


use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Vcomments */

$this->title = 'Update Vcomments: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '视频列表', 'url' => ['/videos/index']];
$this->params['breadcrumbs'][] = ['label' => '评论管理', 'url' => [Url::to(['/vcomments/index','vid'=>$model->vid]) ]];
$this->params['breadcrumbs'][] = ['label' => '评论ID: '.$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="vcomments-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
