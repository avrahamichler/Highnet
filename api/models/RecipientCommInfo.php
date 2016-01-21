<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "recipient_comm_info".
 *
 * @property string $recipient_code
 * @property string $output_device_code
 * @property string $data
 * @property boolean $is_default
 * @property boolean $is_active
 * @property integer $comm_escalation_type
 */
class RecipientCommInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipient_comm_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_default', 'is_active'], 'boolean'],
            [['comm_escalation_type'], 'integer'],
            [['recipient_code'], 'string', 'max' => 40],
            [['output_device_code'], 'string', 'max' => 16],
            [['data'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'recipient_code' => 'Recipient Code',
            'output_device_code' => 'Output Device Code',
            'data' => 'Data',
            'is_default' => 'Is Default',
            'is_active' => 'Is Active',
            'comm_escalation_type' => 'Comm Escalation Type',
        ];
    }
}
