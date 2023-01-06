<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rolspeler".
 *
 * @property int $id
 * @property string $naam
 * @property int $actief
 * @property int $beschikbaar
 *
 * @property Gesprek[] $gespreks
 */
class Rolspeler extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rolspeler';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['naam', 'token'], 'required'],
            [['actief', 'beschikbaar'], 'integer'],
            [['naam'], 'string', 'min' => 5, 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'naam' => 'Naam',
            'actief' => 'Actief',
            'beschikbaar' => 'Beschikbaar',
            'token' => 'Token',
        ];
    }

    /**
     * Gets query for [[Gespreks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGespreks()
    {
        return $this->hasMany(Gesprek::className(), ['rolspelerid' => 'id']);
    }
    
}
