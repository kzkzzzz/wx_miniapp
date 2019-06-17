<?php
namespace api\modules\v1\controllers;

use Yii;
use api\models\Videos;
use yii\data\ActiveDataProvider;
use api\models\Vcomments;
use function GuzzleHttp\Psr7\readline;
use yii\caching\DbDependency;

class VideosController extends BaseController
{
    public $modelClass = 'api\models\Videos';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['index', 'search', 'recommend','vcomment','recordplay'];
        // unset($behaviors['authenticator']);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }


    public function actionIndex()
    {
        $query = Videos::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
                'validatePage' => false
            ]
        ]);
        return $dataProvider;
    }

    public function actionSearch()
    {
        $keyword = Yii::$app->request->get('keyword');
        $query = Videos::find();
        $result = $query->filterWhere(['like', 'title', $keyword])->all();
        return $result;
    }

    public function actionLike()
    {
        $vid = Yii::$app->request->get('vid');
        $f = Yii::$app->request->get('f');

        if (!$vid || $f === null) return ['msg' => 'count not doing'];

        if (Videos::updateAllCounters(['thumbs' => $f ? 1 : -1], ['id' => $vid])) {
            return ['status' => 0, 'msg' => $f ? '点赞成功' : '取消点赞'];
        } else {
            return ['status' => 1, 'msg' => '点赞失败'];
        }
    }

    public function actionRecommend()
    {
        $id = Yii::$app->request->get('id');
        if (!$id) return ['msg' => 'no recommend'];

        $cacheRemArray = Yii::$app->cache->get('cacheRemArray' . $id);

        if ($cacheRemArray == false) {
            $randArr = array_diff(range(1, 20), [$id]);
            shuffle($randArr);
            $reArr = array_slice($randArr, 0, 6);
            Yii::$app->cache->set('cacheRemArray' . $id, $reArr, 120);
            $cacheRemArray = Yii::$app->cache->get('cacheRemArray' . $id);
        }

        $remVideos = Videos::find()->where(['in', 'id', $cacheRemArray])->all();
        
        return $remVideos;
        

    }



    public function actionVcomment(){
        $vid = Yii::$app->request->get('vid');
        if(!$vid)return [];
        /* $sql = Yii::$app->db->createCommand('select count(*) from vcomments where vid = :vid')
                ->bindParam(':vid',$vid)
                ->getRawSql();
         */
        $dependency = new DbDependency([
            'sql'=>'select count(*) from vcomments'
        ]);

        $cacheComment = Yii::$app->cache->get('cacheComment'.$vid);

        if($cacheComment == false){
            $comment = Videos::findOne($vid)->vcomments;
            $result = ['comments'=>$comment,'comment_num'=>count($comment)];

            Yii::$app->cache->set('cacheComment'.$vid, $result, 600, $dependency);
            $cacheComment = Yii::$app->cache->get('cacheComment' . $vid);
        }

        // Yii::$app->cache->delete('cacheComment1');
        
        return $cacheComment;
    }

    public function actionSavecomment(){
        $data = Yii::$app->request->bodyParams;
        $model = new Vcomments();
        $model->attributes = $data;
        if($model->validate()){
            if($model->save()){
                return ['msg'=>'评论成功','status'=>0,'new_comment'=>Vcomments::findOne($model->id)];
            }else{
                return ['msg'=>'评论失败，稍后再试','status'=>2];
            }
        } else{
            return array_merge(['errors'=>$model->errors],['status'=>1]);
        }
    }




    public function test()
    {
        $fileDir = dir(getcwd() . '/videos');
        $rr = [];
        // $fs = 'D:\wamp\www\apiyii\api\web\videos\a.mp4';
        // exec('D:\其他工具\ffmpeg\ffmpeg-20190604-d81913e-win64-static\bin\ffmpeg.exe -i '.$fs.' -y -f mjpeg -ss 5 -vframes 1 '.getcwd().'/images/'.'a.jpg');            

        /* while (($file =$fileDir->read()) != false) {
            exec('D:\其他工具\ffmpeg\ffmpeg-20190604-d81913e-win64-static\bin\ffmpeg.exe -i '.getcwd().'/videos/'.$file.' -y -f mjpeg -ss 5 -vframes 1 '.getcwd().'/images/'.$file.'.jpg');            
            $rr[] = $file;
        } */
        return ['s' => $rr];
    }

    public function actionRecordplay(){
        $vid = Yii::$app->request->headers['recordvid'];
        // return $vid;
        // $vid = Yii::$app->request->get('vid');
        if($vid){
            if(Videos::updateAllCounters(['play_num'=>1],['id'=>$vid])){
                return 'ok';
            }
        };

    }
}
