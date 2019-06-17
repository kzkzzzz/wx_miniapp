<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "videos".
 *
 * @property int $id
 * @property string $title
 * @property string $play_url
 * @property string $img_url
 * @property int $thumbs
 * @property int $play_num
 * @property int $created_at
 * @property int $updated_at
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
            [['title', 'play_url', 'img_url', 'thumbs', 'play_num'], 'required'],
            // [['thumbs', 'play_num'], 'integer'],
            [['title', 'play_url', 'img_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '视频标题',
            'play_url' => '播放地址',
            'img_url' => '视频封面',
            'thumbs' => '点赞数',
            'play_num' => '播放数',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
