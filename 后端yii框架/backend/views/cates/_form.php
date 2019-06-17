<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Cates */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cates-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'catename')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
