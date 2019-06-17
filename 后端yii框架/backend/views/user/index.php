<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.proimg{
    max-width: 5vw;
    max-height: 4.5vw;
}
</style>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p style="height:1px"></p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'profile_url',
                'value'=>function($model){
                    $html = Html::img($model->profile_url,['class'=>'proimg']);
                    return $html;
                },
                'format'=>'html'
            ],
            'openid',
            'username',
            'phone',
            'email:email',
            //'profile_url:url',
            //'access_token',
            //'session_key',
            //'access_time:datetime',
            //'password_hash',
            //'status',
            //'created_at',
            //'updated_at',
            //'auth_key',
            //'password_reset_token',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view} {delete}',
                'header'=>'操作'
            ],
        ],
    ]); ?>


</div>
