<?php
namespace api\modules\v1\controllers;

use Yii;
use api\models\User;
use yii\caching\DbDependency;

class UserController extends BaseController
{
    public $modelClass = 'api\models\User';

    public function behaviors(){
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['order'];
        return $behaviors;
    }

    public function actionCheck(){
        return ['userid'=>Yii::$app->user->identity->id];
    }

    public function actionOrder(){
        $id = Yii::$app->request->get('userid');
        
        // $sql = Yii::$app->db->createCommand('select count(*) from oders where user_id=:id')->bindParam(':id',$id)->getRawSql();
        // $dependency = new DbDependency(['sql'=>'select count(*) from orders']);

        $user = User::findOne($id);
        if(empty($user))return ['msg'=>'user null'];
        if(!empty($user->orders)){
            return ['orders'=>$user->orders];
        } else{
            return ['orders'=>null];
        }

    }
}