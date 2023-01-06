<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Criterium;

/**
 * CriteriumSearch represents the model behind the search form of `app\models\Criterium`.
 */
class CriteriumSearch extends Criterium
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cruciaal'], 'integer'],
            [['omschrijving', 'nul', 'een', 'twee', 'drie', 'werkprocesid'], 'safe'],
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
        $query = Criterium::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cruciaal' => $this->cruciaal,
        ]);

        $query->andFilterWhere(['like', 'omschrijving', $this->omschrijving])
            ->andFilterWhere(['like', 'nul', $this->nul])
            ->andFilterWhere(['like', 'een', $this->een])
            ->andFilterWhere(['like', 'twee', $this->twee])
            ->andFilterWhere(['like', 'drie', $this->drie])
            ->andFilterWhere(['like', 'werkprocesid', $this->werkprocesid]);

        return $dataProvider;
    }
}
