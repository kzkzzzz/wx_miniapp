<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property string $goods
 * @property string $total_price
 * @property string $discount_price
 * @property string $address
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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    /**
     * {@inheritdoc}
     */
    /* public function rules()
    {
        return [
   
        ];
    } */

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['user_id'], $fields['updated_at']);

        $fields['goods'] = function ($model) {
            $goodsModel = Goods::find()->indexBy('id')->asArray()->all();
            $goodsIdArr = explode(',', $model->goods);//把商品记录字符串拆分成数组
            foreach ($goodsIdArr as $value) {
                $valueArr = explode('-', $value);
                $temp = [];
                $temp['gid'] = $valueArr[0];
                $temp['gtotal'] = $goodsModel[$valueArr[0]]['gname'] . ' x' . $valueArr[1] .' ￥'.intval($valueArr[1]) * $goodsModel[$valueArr[0]]['gprice'];
                $temp['gimage'] = $goodsModel[$valueArr[0]]['gimage'];
                $arr[] = $temp;
            }
            return $arr;
        };
        $fields['created_at'] = function ($model) {
            return date('Y-m-d H:i:s', $model->created_at);
        };
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'goods' => 'Goods',
            'total_price' => 'Total Price',
            'discount_price' => 'Discount Price',
            'address' => 'Address',
        ];
    }

    public function createOrder($bodyParams)
    {
        sleep(2);
        
        // $goodsModel = Goods::find()->indexBy('id')->asArray()->all();
        /* if(!in_array($bodyParams['key'],['lizifeng'])){
           return ['status'=>1,'msg'=>'key不正确'];
        }; */
        if($bodyParams['key'] != 'cdkey'.$bodyParams['price']['totalPrice']){
            return ['status'=>1,'msg'=>'cdkey不正确'];
        };
        if (count($bodyParams['goods']) < 1) return ['msg' => 'goods null'];

        $goodArr = [];
        foreach ($bodyParams['goods'] as $good) {
            $goodArr[] = $good['gid'] . '-' . $good['total'];
            Goods::updateAllCounters(['gsale'=>$good['total']],['id'=>$good['gid']]);
        }
        
        $goodStr = implode(',', $goodArr);

        $order = new Orders();
        $order->user_id = $bodyParams['userid'];
        $order->goods = $goodStr;
        $order->total_price = (float)$bodyParams['price']['totalPrice'];
        $order->discount_price = (float)$bodyParams['price']['discountPrice'];
        $order->consignee = $bodyParams['address']['name'];
        $order->phone = $bodyParams['address']['phone'];
        $order->address = $bodyParams['address']['address'];
        $order->ordernum = 'OD' . date('YmdHis', time()) . (100 + Orders::find()->count());
        $order->save();
        return ['status'=>0,'msg'=>'支付成功','ordernum'=>$order->ordernum,'userid'=>$order->user_id];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->total_price = floatval($this->total_price);
        $this->discount_price = floatval($this->discount_price);
    }
}
