<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use backend\models\User;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property string $goods
 * @property string $total_price
 * @property string $discount_price
 * @property string $consignee
 * @property string $phone
 * @property string $address
 * @property string $ordernum
 * @property int $created_at
 * @property int $updated_at
 */
class Orders extends ActiveRecord
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
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['consignee', 'phone', 'address'], 'required'],
            // [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['total_price', 'discount_price'], 'number'],
            [['goods', 'consignee', 'phone', 'address', 'ordernum'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'goods' => '商品',
            'total_price' => '实付',
            'discount_price' => '已折扣',
            'consignee' => '收货人',
            'phone' => '手机号',
            'address' => '地址',
            'ordernum' => '订单号',
            'created_at' => '下单时间',
            'updated_at' => 'Updated At',
        ];
    }

    public function afterFind()
    {
      $this->created_at = date('Y-m-d H:i:s',$this->created_at);
    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }
}