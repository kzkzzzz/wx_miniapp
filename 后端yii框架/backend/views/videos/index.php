<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VideosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '视频管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.video_img{
    max-width: 7vw;
    max-height: 5vw;
}
</style>
<div class="videos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('添加视频', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                'attribute'=>'img_url',
                'value'=>function($model){
                    $html = Html::img($model->img_url,['class'=>'video_img']);
                    return $html;
                },
                'format'=>'html'
            ],
            [
                'attribute'=>'title',
                'value'=>function($model){
                    $titleLen = strlen($model->title);
                    return mb_substr($model->title,0,20).($titleLen>20?'...':'');
                },
                'contentOptions'=>['width'=>'15%']
            ],
            [
                'attribute'=>'play_url',
                'value'=>function($model){
                    $html = Html::a('点击播放',$model->play_url,['target'=>'_blank']);
                    return $html;
                },
                'format'=>'raw'
            ],
            'thumbs',
            'play_num',

            [
                'class' =>'yii\grid\ActionColumn',
                'header'=>'操作',
                'template'=>'{comment} {view} {update} {delete}',
                'buttons'=>[
                    'comment'=>function($url,$model,$key){
                        $a = Html::a('<span class="glyphicon glyphicon-comment"></span>',Url::to(['/vcomments/index','vid'=>$model->id]),[
                            'aria-label'=>'评论管理',
                            'title'=>'评论管理'
                        ]);
                        return $a;
                    },

                ]
            ],
        ],
    ]); ?>


</div>
