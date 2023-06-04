<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Uitslag;

/**
 * UitslagSearch represents the model behind the search form of `app\models\Uitslag`.
 */
class UitslagSearch extends Uitslag
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'studentid', 'examenid', 'beoordeelaar1id', 'beoordeelaar2id', 'ready'], 'integer'],
            [['werkproces', 'commentaar', 'resultaat', 'timestamps'], 'safe'],
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
        $query = Uitslag::find();

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
            'examenid' => $this->examenid,
            'beoordeelaar1id' => $this->beoordeelaar1id,
            'beoordeelaar2id' => $this->beoordeelaar2id,
            'ready' => $this->ready,
            'timestamps' => $this->timestamps,
        ]);

        $query->andFilterWhere(['like', 'werkproces', $this->werkproces])
            ->andFilterWhere(['like', 'commentaar', $this->commentaar])
            ->andFilterWhere(['like', 'resultaat', $this->resultaat]);

        return $dataProvider;
    }
}
