<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;


use backend\models\LoginForm;
use yii\web\Response;
use yii\web\UploadedFile;
use backend\models\Goods;
use yii\web\NotFoundHttpException;
use yii\imagine\Image;
/**
 * Site controller
 */
class SiteController extends AccessController
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // return $this->render('index');
        return $this->redirect('/goods/index');
    }


    public function actionUpload()
    {
        $model = new Goods();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model->scenario = 'upload';
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $model->good_image = UploadedFile::getInstance($model, 'good_image');

            if ($model->validate()) {
                $fileName = md5(date('YmdHis') . mt_rand(1000, 9999)) .'.'.$model->good_image->getExtension();
                $model->good_image->saveAs('./uploadtemp/'.$fileName);
                // Yii::getAlias('@backend/web/uploadtemp');

                // Image::resize('uploadtemp/'.$fileName,300,300)->save('uploadtemp/'.$fileName);

                /* $imageSize = getimagesize('uploadtemp/'.$fileName);

                Image::text('uploadtemp/'.$fileName,'MAPLEGG',getcwd().'/font/phetsarath.ttf',
                [ceil($imageSize[0])-105,ceil($imageSize[1])-20],['size'=>15])->save('uploadtemp/'.$fileName); */


                $this->asyncFtp($fileName,'uploadtemp/'.$fileName);
                return [
                    'code'=>0,
                    'msg'=>'ok',
                    'ext'=>$model->good_image->getExtension(),
                    'time'=>date('Y-m-d H:i:s'),
                    'data'=>[
                        'url'=>'https://www.channelcc.cc/maplegg/upload/'.date('Ymd').'/'.$fileName
                    ]
                ];
            } else {
                return array_merge($model->errors,['ss'=>getcwd().'/font/phetsarath.ttf' ]);
            }
        }
        return 's';
    }

    public function asyncFtp($filename,$filepath)
    {

        $fp = @fsockopen('www.apiyii.ae', 80, $errno, $errstr);
        $http = "GET /site/ftpcc?hide=cc&filename={$filename}&filepath={$filepath} HTTP/1.1\r\n";
        $http .= "Host: www.apiyii.ae\r\n";
        $http .= "Connection: close\r\n\r\n";
        $ss = '';
        fputs($fp, $http);
        fclose($fp);

    }

    public function actionFtpcc()
    {
        /* $file = Yii::$app->request->get('file');
        file_put_contents('D:\wamp\www\php123\f.txt',$file); */

        if (!Yii::$app->request->get('hide')) {
            throw new NotFoundHttpException('404错误');
        }
        $filename = Yii::$app->request->get('filename');
        $filepath = Yii::$app->request->get('filepath');

        $ftpcooc = ftp_connect('35.197.158.84');
        ftp_login($ftpcooc, 'imagephp', 'er129126');

        ftp_chdir($ftpcooc, '/maplegg/upload');
        $ch = @ftp_chdir($ftpcooc, date('Ymd'));
        if ($ch == false) {
            ftp_mkdir($ftpcooc, '/maplegg/upload/' . date('Ymd'));
            @ftp_chdir($ftpcooc, date('Ymd'));
        } else {
            @ftp_chdir($ftpcooc, date('Ymd'));
        }

        $res = ftp_put($ftpcooc, $filename, $filepath, FTP_BINARY);
        return $res;
        ftp_close($ftpcooc);
    }

}
