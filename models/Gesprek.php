<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Gesprek".
 *
 * @property int $id
 * @property int $formid
 * @property int|null $rolspelerid
 * @property int $studentid
 * @property string|null $opmerking
 * @property int|null $status
 */
class Gesprek extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gesprek';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formid', 'studentid'], 'required'],
            [['formid', 'rolspelerid', 'studentid', 'status', 'statusstudent'], 'integer'],
            [['created',   ], 'safe'],
            [['opmerking'], 'string', 'max' => 200],
            [['studentid'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['studentid' => 'id']],
            [['rolspelerid'], 'exist', 'skipOnError' => true, 'targetClass' => Rolspeler::className(), 'targetAttribute' => ['rolspelerid' => 'id']],
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
            'rolspelerid' => 'Rolspeler',
            'studentid' => 'Studentid',
            'opmerking' => 'Opmerking',
            'status' => 'Status',
            'created' => 'Created',
            'statusstudent' => 'statusstudent',
        ];
    }

    public function getForm()
    {
        return $this->hasOne(Form::className(), ['id' => 'formid']);
    }

    public function getExamen() {
        return $this->hasOne(Examen::className(), ['id' => 'examenid'])->via('form');
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentid']);
    }

    public function getRolspeler()
    {
        return $this->hasOne(Rolspeler::className(), ['id' => 'rolspelerid']);
    }

    public function getBeoordeling()
    {
        return $this->hasOne(Beoordeling::className(), ['gesprekid' => 'id']);
    }
}
