<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "results".
 *
 * @property int $id
 * @property int $studentid
 * @property int $formid
 * @property int $vraagid
 * @property int|null $vraagnr
 * @property int $antwoordnr
 * @property int $score
 * @property string $timestamp
 */
class Results extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'results';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studentid', 'formid', 'vraagid', 'antwoordnr', 'score'], 'required'],
            [['studentid', 'formid', 'vraagid', 'vraagnr', 'antwoordnr', 'score'], 'integer'],
            [['timestamp'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studentid' => 'Studentid',
            'formid' => 'Formid',
            'vraagid' => 'Vraagid',
            'vraagnr' => 'Vraagnr',
            'antwoordnr' => 'Antwoordnr',
            'score' => 'Score',
            'timestamp' => 'Timestamp',
        ];
    }
    public function getForm()
    {
        return $this->hasOne(Form::className(), ['id' => 'formid']);
    }

    public function getVraag()
    {
        return $this->hasOne(Vraag::className(), ['id' => 'vraagid']);
    }

}
