<?php

namespace backend\models;

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
            [['content'], 'required'],
            // [['vid', 'uid', 'created_at', 'updated_at'], 'integer'],
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
            'vid' => '视频id',
            'uid' => '用户id',
            'content' => '评论内容',
            'created_at' => '发表时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'uid']);
    }
}
