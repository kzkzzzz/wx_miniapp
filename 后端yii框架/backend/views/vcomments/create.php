<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Vcomments */

$this->title = 'Create Vcomments';
$this->params['breadcrumbs'][] = ['label' => 'Vcomments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vcomments-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
