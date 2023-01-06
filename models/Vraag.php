<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vraag".
 *
 * @property int $id
 * @property int $formid
 * @property int $volgnr
 * @property string $vraag
 * @property string|null $toelichting
 * @property int|null $ja
 * @property int|null $soms
 * @property int|null $nee
 * @property int|null $mappingid
 *
 * @property Form $form
 */
class Vraag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vraag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formid', 'volgnr', 'vraag'], 'required'],
            [['formid', 'volgnr', 'ja', 'soms', 'nee', 'mappingid','standaardwaarde'], 'integer'],
            [['vraag'], 'string', 'max' => 200],
            [['toelichting'], 'string', 'max' => 500],
            [['formid'], 'exist', 'skipOnError' => true, 'targetClass' => Form::className(), 'targetAttribute' => ['formid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'formid' => 'Formid',
            'volgnr' => 'Volgnr',
            'vraag' => 'Vraag',
            'toelichting' => 'Toelichting',
            'ja' => 'Ja',
            'soms' => 'Soms',
            'nee' => 'Nee',
            'mappingid' => 'Mappingid',
            'standaardwaarde' => 'default'
        ];
    }

    /**
     * Gets query for [[Form]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForm() {
        return $this->hasOne(Form::className(), ['id' => 'formid']); // pk.relation => fk.model
    }
    public function getExamen() {
        return $this->hasOne(Examen::className(), ['id' => 'examenid'])->via('form');
    }
    public function getCriterium() {
        return $this->hasOne(Criterium::className(), ['id' => 'mappingid']);
    }
}
