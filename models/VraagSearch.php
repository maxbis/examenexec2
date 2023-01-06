<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Vraag;

/**
 * VraagSearch represents the model behind the search form of `app\models\Vraag`.
 */
class VraagSearch extends Vraag
{
    /**
     * {@inheritdoc}
     */

    // added for N:1 relation vraag:form
    public $formNaam;

    public function rules()
    {   
        // added formNaam for N:1 relation vraag:form
        return [
            [['id', 'formid', 'volgnr', 'ja', 'soms', 'nee', 'mappingid'], 'integer'],
            [['vraag', 'formNaam'], 'safe'],
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
        // added innerjoin for N:1 relation vraag:form
        $query = Vraag::find()
            ->joinwith('examen')
            ->joinwith('form')
            ->where(['examen.actief'=>1])
            ->andwhere(['form.actief'=>1])
            ->orderBy('form.nr');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);
        
        // added for N:1 relation vraag:form
        $dataProvider->sort->attributes['formNaam'] = [  
            'asc' => ['form.omschrijving' => SORT_ASC],
            'desc' => ['form.omschrijving' => SORT_DESC],
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
            'volgnr' => $this->volgnr,
            'ja' => $this->ja,
            'soms' => $this->soms,
            'nee' => $this->nee,
            'mappingid' => $this->mappingid,
        ]);

        $query->andFilterWhere(['like', 'vraag', $this->vraag])
        ->andFilterWhere(['like', 'form.omschrijving', $this->formNaam]);;

        return $dataProvider;
    }
}
