<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "werkproces".
 *
 * @property string $id
 * @property int $maxscore
 * @property int $examen_type
 */
class Werkproces extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'werkproces';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'maxscore', 'examen_type'], 'required'],
            [['titel'], 'string', 'max' => 200],
            [['maxscore', 'examen_type'], 'integer'],
            [['id'], 'string', 'max' => 8],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'WP Naam',
            'titel' => 'Titel',
            'maxscore' => 'SPL Maxscore',
            'examen_type' => 'Examen Type',
        ];
    }

    public function getForm() {
        return $this->hasMany(Form::className(), ['werkproces' => 'id']); // key.relation => key.model
    }

    public function getVraag() {
        return $this->hasMany(Vraag::className(), ['formid' => 'id'])->via('form'); // key.relation => key.model
    }

    public function getExamen() {
        return $this->hasMany(Examen::className(), ['examen_type' => 'examen_type']); // key.relation => key.model
    }
}
