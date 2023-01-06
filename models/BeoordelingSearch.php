<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Beoordeling;

/**
 * BeoordelingSearch represents the model behind the search form of `app\models\Beoordeling`.
 */
class BeoordelingSearch extends Beoordeling
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'formid', 'studentid', 'rolspelerid'], 'integer'],
            [['resultaat', 'opmerking', 'timestamp'], 'safe'],
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
        $query = Beoordeling::find();

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
            'formid' => $this->formid,
            'studentid' => $this->studentid,
            'rolspelerid' => $this->rolspelerid,
            'timestamp' => $this->timestamp,
        ]);

        $query->andFilterWhere(['like', 'resultaat', $this->resultaat])
            ->andFilterWhere(['like', 'opmerking', $this->opmerking]);

        return $dataProvider;
    }
}
