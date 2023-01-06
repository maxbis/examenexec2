<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property int $nummer
 * @property string $naam
 * @property string $klas
 *
 * @property Gesprek[] $gespreks
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student';
    }

    public function getAchternaam() {
        return strrchr($this->naam,' ');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nummer', 'naam', 'klas'], 'required'],
            [['nummer', 'actief'], 'integer'],
            [['nummer'], 'integer', 'max' => 9999999 ],
            [['naam'], 'string', 'max' => 30],  // ['naam', 'match', 'pattern' => '/^([a-z]\w){1,4}$/i'],
            [['klas'], 'string', 'max' => 2],
            [['locatie'], 'string', 'max' => 6],
            [['message'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nummer' => 'Nummer',
            'naam' => 'Naam',
            'klas' => 'Klas',
            'locatie' => 'Locatie',
            'message' => 'Message',
            'actief' => 'Actief',
            'achternaam' => 'Achternaam',
        ];
    }

    /**
     * Gets query for [[Gespreks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGesprekken()
    {
        return $this->hasMany(Gesprek::className(), ['studentid' => 'id']);
    }

    public function getAantalGesprekken()
    {
        return $this->hasMany(Gesprek::className(), ['studentid' => 'id'])->count();
    }

}
