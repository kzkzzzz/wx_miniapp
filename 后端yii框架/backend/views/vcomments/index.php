<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VcommentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '评论管理';
$this->params['breadcrumbs'][] = ['label' => '视频列表', 'url' => ['/videos/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vcomments-index">

    <h1><?= Html::encode($this->title) ?></h1>

<!--     <p>
        <?= Html::a('Create Vcomments', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'id',
                'contentOptions'=>['width'=>'5%']
            ],
            [
                'attribute'=>'content',
                'value'=>function($model){
                    $contentLen = strlen($model->content);
                    return mb_substr($model->content,0,60).($contentLen>20?'...':'');
                }
            ],
            [
                'attribute'=>'user.username'
            ],
            [
                'attribute'=>'created_at',
                'value'=>function($model){
                    return date('Y-m-d H:i:s',$model->created_at);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'操作'
            ],
        ],
    ]); ?>


</div>
