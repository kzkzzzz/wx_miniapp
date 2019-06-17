<?php

namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vcomments".
 *
 * @property int $id
 * @property int $vid
 * @property int $uid
 * @property string $content
 * @property int $created_at
 * @property int $updated_at
 */
class Vcomments extends ActiveRecord
{

    private $shaha;

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
        return 'vcomments';
    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /* [['id', 'vid', 'uid', 'content', 'created_at', 'updated_at'], 'required'],
            [['id', 'vid', 'uid', 'created_at', 'updated_at'], 'integer'], */
            [['uid','vid'],'required','message'=>'缺少必要的字段'],
            ['content','required','message'=>'评论内容不能为空'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vid' => 'Vid',
            'uid' => 'Uid',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields(){
        return[
            'id',
            'vid',
            'comment_user' =>function($model){
                return $model->user->username;
            },
            'content',
            'created_at',
            'updated_at',
            'profile'=>function($model){
                return $model->user->profile_url;
            }
        ];
        
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'uid']);
    }

    public function getVideo(){
        return $this->hasOne(Videos::className(),['id'=>'vid']);
    }

    public function afterFind()
    {
        // $this->vid = $this->video->title;
        // $this->uid = $this->user->username;
        $this->created_at = date('Y-m-d H:i:s',$this->created_at);
        $this->updated_at = date('Y-m-d H:i:s',$this->updated_at);
    }

    /* public function getShaha(){
        return $this->content;
    } */
}
