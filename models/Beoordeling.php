<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "beoordeling".
 *
 * @property int $id
 * @property int $formid
 * @property int $studentid
 * @property int $rolspelerid
 * @property string $resultaat
 * @property string|null $opmerking
 * @property string $timestamp
 *
 * @property Form $form
 * @property Rolspeler $rolspeler
 * @property Student $student
 * @property Score[] $scores
 */
class Beoordeling extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'beoordeling';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gesprekid', 'formid', 'studentid', 'rolspelerid', 'resultaat'], 'required'],
            [['gesprekid', 'formid', 'studentid', 'rolspelerid'], 'integer'],
            [['resultaat', 'opmerking'], 'string'],
            [['timestamp'], 'safe'],
            [['formid'], 'exist', 'skipOnError' => true, 'targetClass' => Form::className(), 'targetAttribute' => ['formid' => 'id']],
            [['rolspelerid'], 'exist', 'skipOnError' => true, 'targetClass' => Rolspeler::className(), 'targetAttribute' => ['rolspelerid' => 'id']],
            [['studentid'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['studentid' => 'id']],
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
            'studentid' => 'Studentid',
            'rolspelerid' => 'Rolspelerid',
            'resultaat' => 'Resultaat',
            'opmerking' => 'Opmerking',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * Gets query for [[Form]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Form::className(), ['id' => 'formid']);
    }

    /**
     * Gets query for [[Rolspeler]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRolspeler()
    {
        return $this->hasOne(Rolspeler::className(), ['id' => 'rolspelerid']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentid']);
    }

    /**
     * Gets query for [[Scores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScores()
    {
        return $this->hasMany(Score::className(), ['beoordelingid' => 'id']);
    }
}
