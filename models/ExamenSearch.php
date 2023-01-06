<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Examen;

/**
 * ExamenSearch represents the model behind the search form of `app\models\Examen`.
 */
class ExamenSearch extends Examen
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'actief'], 'integer'],
            [['naam', 'datum_start', 'datum_eind'], 'safe'],
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
        $query = Examen::find();

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
            'actief' => $this->actief,
            'datum_start' => $this->datum_start,
            'datum_eind' => $this->datum_eind,
        ]);

        $query->andFilterWhere(['like', 'naam', $this->naam]);

        return $dataProvider;
    }
}
