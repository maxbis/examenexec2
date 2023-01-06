<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uitslag".
 *
 * @property int $id
 * @property int $studentid
 * @property int $examenid
 * @property int $werkproces
 * @property int|null $beoordeelaar1id
 * @property int|null $beoordeelaar2id
 * @property string|null $commentaar
 * @property int|null $ready
 * @property string|null $resultaat
 */
class Uitslag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uitslag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studentid', 'examenid', 'werkproces', 'beoordeelaar1id', 'beoordeelaar2id'], 'required'],
            [['id', 'studentid', 'examenid', 'beoordeelaar1id', 'beoordeelaar2id', 'ready'], 'integer'],
            [['werkproces', 'commentaar', 'resultaat'], 'string'],
            [['werkproces'], 'string', 'max' => 8],
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
            'examenid' => 'Examenid',
            'werkproces' => 'Werkproces',
            'beoordeelaar1id' => 'Beoordeelaar1id',
            'beoordeelaar2id' => 'Beoordeelaar2id',
            'commentaar' => 'Commentaar',
            'ready' => 'Ready',
            'resultaat' => 'Resultaat',
        ];
    }

    public function getRolspeler()
    {
        return $this->hasOne(Rolspeler::className(), ['id' => 'beoordeelaar1id']);
    }

    public function getRolspeler1()
    {
        return $this->hasOne(Rolspeler::className(), ['id' => 'beoordeelaar1id']);
    }

    public function getRolspeler2()
    {
        return $this->hasOne(Rolspeler::className(), ['id' => 'beoordeelaar2id']);
    }
}
