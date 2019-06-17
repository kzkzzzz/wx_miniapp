<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $openid
 * @property string $username
 * @property string $phone
 * @property string $email
 * @property string $profile_url
 * @property string $access_token
 * @property string $session_key
 * @property int $access_time
 * @property string $password_hash
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $auth_key
 * @property string $password_reset_token
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['openid', 'username'], 'required'],
            [['access_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['openid', 'session_key'], 'string', 'max' => 60],
            [['username', 'phone', 'email', 'profile_url', 'access_token', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['openid'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            'openid' => 'Openid',
            'username' => '用户名',
            'phone' => '手机号',
            'email' => '邮箱',
            'profile_url' => '头像',
            'access_token' => 'Access Token',
            'session_key' => 'Session Key',
            'access_time' => 'Access Time',
            'password_hash' => 'Password Hash',
            'status' => 'Status',
            'created_at' => '注册时间',
            'updated_at' => 'Updated At',
            'auth_key' => 'Auth Key',
            'password_reset_token' => 'Password Reset Token',
        ];
    }
}
