<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Gesprek;

/**
 * GesprekSearch represents the model behind the search form of `app\models\Gesprek`.
 */
class GesprekSearch extends Gesprek
{
    /**
     * {@inheritdoc}
     */

    public $student;
    public $locatie;

    public function rules()
    {
        return [
            [['formid', 'rolspelerid', 'status','statusstudent'], 'integer'],
            [['opmerking', 'student', 'locatie'], 'safe'],
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
   
        $query = Gesprek::find()
            ->joinwith(['examen', 'form', 'student'])
            ->where(['examen.actief'=>1])
            ->andwhere(['form.actief'=>1]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['status' => SORT_ASC, 'id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['student'] = [
            'asc' => ['student.naam' => SORT_ASC],
            'desc' => ['student.naam' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['locatie'] = [
            'asc' => ['student.locatie' => SORT_ASC],
            'desc' => ['student.locatie' => SORT_DESC],
        ];

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
            'rolspelerid' => $this->rolspelerid,
            'status' => $this->status,
            'statusstudent' => $this->statusstudent,
        ]);
        $query->andFilterWhere(['like', 'opmerking', $this->opmerking]);
        $query->andFilterWhere(['like', 'student.naam', $this->student]);
        // $query->andFilterWhere(['like', 'student.klas', $this->student]);
        $query->andFilterWhere(['like', 'student.locatie', $this->locatie]);

        return $dataProvider;
    }
}
