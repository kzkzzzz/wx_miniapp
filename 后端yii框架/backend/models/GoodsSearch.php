<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Goods;

/**
 * GoodsSearch represents the model behind the search form of `backend\models\Goods`.
 */
class GoodsSearch extends Goods
{

    public function attributes()
    {
        return array_merge(parent::attributes(),['catename']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cate_id', 'gsale'], 'integer'],
            [['gname', 'gdesc', 'gimage',"catename"], 'safe'],
            [['gprice', 'disprice'], 'number'],
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
    public function search($params)
    {
        $query = Goods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>[
                'pageSize'=>25
            ],
            'sort'=>[
                'defaultOrder'=>[
                    'cate_id'=>SORT_ASC,
                    'id'=>SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cate_id' => $this->cate_id,
            'gsale' => $this->gsale,
            'gprice' => $this->gprice,
            'disprice' => $this->disprice,
        ]);

        $query->andFilterWhere(['like', 'gname', $this->gname])
            ->andFilterWhere(['like', 'gdesc', $this->gdesc])
            ->andFilterWhere(['like', 'gimage', $this->gimage]);
            
        $query->innerJoin('cates','goods.cate_id = cates.id');
        $query->andFilterWhere(['like','cates.catename',$this->catename]);

        $dataProvider->sort->attributes['catename'] = [
            'asc'=>['cates.catename'=>SORT_ASC],
            'desc'=>['cates.catename'=>SORT_DESC],
        ];

        return $dataProvider;
    }
}
