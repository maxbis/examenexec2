<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UitslagLog;

/**
 * UitslagLogSearch represents the model behind the search form of `app\models\UitslagLog`.
 */
class UitslagLogSearch extends UitslagLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'studentid', 'cijfer', 'old_cijfer'], 'integer'],
            [['naam', 'werkproces', 'resultaat', 'timestamp'], 'safe'],
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
        $query = UitslagLog::find();

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
            'studentid' => $this->studentid,
            'cijfer' => $this->cijfer,
            'old_cijfer' => $this->old_cijfer,
            'timestamp' => $this->timestamp,
        ]);

        $query->andFilterWhere(['like', 'naam', $this->naam])
            ->andFilterWhere(['like', 'werkproces', $this->werkproces])
            ->andFilterWhere(['like', 'resultaat', $this->resultaat]);

        return $dataProvider;
    }
}
