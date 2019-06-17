<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Cates;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
  .breadcrumb a {
    color: #337ab7 !important;
  }

  .at-input {
    width: 10%;
    text-align: center;
  }

  .pre-image {
    max-width: 7vw;
    max-height: 6.5vw;
    margin-bottom: 1vw;
  }

  #layui-upload {
    margin-bottom: 1vw;
  }
</style>
<div class="goods-form">

  <?php $form = ActiveForm::begin([
    'fieldConfig' => [
      'inputOptions' => ['class' => 'form-control at-input'],
    ]
  ]); ?>

  <?= $form->field($model, 'cate_id')->label('商品分类')
    ->dropDownList(Cates::find()->select(['catename'])->indexBy('id')->column()) ?>

  <?= $form->field($model, 'gname')->textInput(['maxlength' => true])->label('商品名称') ?>

  <?= $form->field($model, 'gsale')->textInput()->label('商品销量') ?>

  <?= $form->field($model, 'gprice')->textInput(['maxlength' => true])->label('商品现价') ?>

  <?= $form->field($model, 'disprice')->textInput(['maxlength' => true])->label('商品原价') ?>
  <?= $form->field($model, 'gimage')->hiddenInput(['id' => 'hide-gimage']) ?>

  <?= Html::img($model->gimage, ['class' => 'pre-image']) ?>
  <?= Html::button('选择图片', ['id' => 'layui-upload', 'class' => 'btn btn-primary']) ?>

  <?= $form->field($model, 'gdesc')->textarea(['maxlength' => true, 'style' => 'width:65%;text-align:left'])->label('商品描述') ?>


  <div class="form-group">
    <?= Html::submitButton('保存提交', ['class' => 'btn btn-success']) ?>
    <?= Html::a('返回首页', \Yii::$app->request->referrer, ['class' => 'btn btn-primary', 'style' => 'color:#ffffff!important']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?= Html::cssFile('/assets/layui/css/layui.css') ?>
<?= Html::jsFile('/assets/layui/layui.js') ?>

<script>
  layui.use('upload', function() {
    var upload = layui.upload;

    var files;
    //layui图片上传
    var uploadInst = upload.render({
      elem: '#layui-upload', //绑定元素
      url: '/site/upload', //上传接口
      accept: 'images',
      field: 'Goods[good_image]',
      choose: function(obj) {
        files = obj.pushFile();
        console.log(files);
        obj.preview(function(index, file, result) {
          $('.pre-image').attr('src', result);
        })
      },
      done: function(res) {
        if (res.code == 0) {
          console.log(res);
          $('#hide-gimage').val(res.data.url);
        } else {
          layer.msg('出错啦', {
            icon: 5,
            time: 2000
          });
        }
        for (var key in files) {
          delete files[key];
        }

      },
      error: function(res) {
        layer.msg('服务器响应失败', {
          icon: 5,
          time: 2000
        });
        for (var key in files) {
          delete files[key];
        }
      },

    });
  });
</script>