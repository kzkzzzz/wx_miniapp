<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\LoginForm;

class LoginController extends AccessController
{
    public $defaultAction = 'login';

    public function actionLogin(){
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();

        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $url = Yii::$app->request->referrer;
            if(!$url){
                return $this->redirect($url);
            }
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }


    public function actionLogout()
    {
        
        Yii::$app->user->logout();

        return $this->goHome();
    }
}