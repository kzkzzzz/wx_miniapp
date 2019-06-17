<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cate_id') ?>

    <?= $form->field($model, 'gname') ?>

    <?= $form->field($model, 'gsale') ?>

    <?= $form->field($model, 'gdesc') ?>

    <?php // echo $form->field($model, 'gprice') ?>

    <?php // echo $form->field($model, 'disprice') ?>

    <?php // echo $form->field($model, 'gimage') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
