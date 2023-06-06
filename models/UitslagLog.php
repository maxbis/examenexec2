<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uitslag_log".
 *
 * @property int $id
 * @property int|null $studentid
 * @property string|null $naam
 * @property string|null $werkproces
 * @property int|null $cijfer
 * @property int|null $old_cijfer
 * @property string|null $resultaat
 * @property string $timestamp
 */
class UitslagLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uitslag_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studentid', 'cijfer', 'old_cijfer'], 'integer'],
            [['resultaat'], 'string'],
            [['timestamp'], 'safe'],
            [['naam'], 'string', 'max' => 30],
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
            'naam' => 'Naam',
            'werkproces' => 'Werkproces',
            'cijfer' => 'Cijfer',
            'old_cijfer' => 'Old Cijfer',
            'resultaat' => 'Resultaat',
            'timestamp' => 'Timestamp',
        ];
    }
}
