<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Vcomments;

/**
 * VcommentsSearch represents the model behind the search form of `backend\models\Vcomments`.
 */
class VcommentsSearch extends Vcomments
{
    public function attributes()
    {
        return array_merge(parent::attributes(),['user.username']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'vid', 'uid', 'created_at', 'updated_at'], 'integer'],
            [['content','user.username'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$vid)
    {
        $query = Vcomments::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->where(['vid'=>$vid]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'vid' => $this->vid,
            'uid' => $this->uid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content]);
        $query->innerJoin('user','vcomments.uid=user.id');

        $query->andFilterWhere(['like','user.username',$this->getAttribute('user.username')]);

        $dataProvider->sort->attributes['user.username'] = [
            'asc'=>['user.username'=>SORT_ASC],
            'desc'=>['user.username'=>SORT_DESC]
        ];

        return $dataProvider;
    }
}
