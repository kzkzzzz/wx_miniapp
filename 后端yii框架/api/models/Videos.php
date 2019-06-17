<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "videos".
 *
 * @property int $id
 * @property string $title
 * @property string $play_url
 * @property string $img_url
 * @property int $thumbs
 * @property int $play_num
 */
class Videos extends ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ]
            ]

        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'videos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'play_url' => 'Play Url',
            'img_url' => 'Img Url',
            'thumbs' => 'Thumbs',
            'play_num' => 'Play Num',
        ];
    }

    public function afterFind()
    {
        $this->created_at = date('Y-m-d',$this->created_at);
        $this->updated_at = date('Y-m-d',$this->updated_at);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['comments'] = function($model){
            return $model->vcomments;
            /* $arr = [];
            foreach ($model->getVcomments()->batch(2) as $vcm) {
                $arr = array_merge($arr,$vcm);
            } */
            
        };
        $fields['comment_num'] = function($model){
            return count($model->vcomments);
        };
        return $fields;
    }

    public function getVcomments(){
        return $this->hasMany(Vcomments::className(),['vid'=>'id'])->orderBy('created_at desc');
    }

}
