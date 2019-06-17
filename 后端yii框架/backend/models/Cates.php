<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cates".
 *
 * @property int $id
 * @property string $catename
 */
class Cates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catename'], 'required'],
            [['catename'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'catename' => '分类名称',
        ];
    }
}
