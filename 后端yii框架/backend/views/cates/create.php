<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Cates */

$this->title = 'Create Cates';
$this->params['breadcrumbs'][] = ['label' => 'Cates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
