<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Criterium".
 *
 * @property int $id
 * @property string $omschrijving
 * @property string $nul
 * @property string $een
 * @property string $twee
 * @property string $drie
 * @property string $werkprocesId
 * @property int $cruciaal
 */
class Criterium extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'criterium';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['omschrijving', 'nul', 'een', 'werkprocesid', 'cruciaal'], 'required'],
            [['cruciaal'], 'integer'],
            [['omschrijving'], 'string', 'max' => 50],
            [['nul', 'een', 'twee', 'drie'], 'string', 'max' => 400],
            [['werkprocesid'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'omschrijving' => 'Omschrijving',
            'nul' => 'Nul',
            'een' => 'Een',
            'twee' => 'Twee',
            'drie' => 'Drie',
            'werkprocesid' => 'Werkproces ID',
            'cruciaal' => 'Cruciaal',
        ];
    }
}
