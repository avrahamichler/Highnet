<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "recipient_mobile_setting".
 *
 * @property integer $index
 * @property integer $recipient_mobile_index
 * @property string $server
 * @property string $backup_server
 * @property integer $timeout_time
 * @property string $unlock_code
 * @property boolean $location_support
 * @property string $picture_file
 * @property string $ittricorder_ver
 */
class RecipientMobileSetting extends \yii\db\ActiveRecord
{

    const  INDEX = 'index';
    const  RECIPIENT_MOBILE_INDEX = 'recipient_mobile_index';
    const  SERVER = 'server';
    const  BACKUP_SERVER = 'backup_server';
    const  TIMEOUT_TIME = 'timeout_time';
    const  UNLOCK_CODE = 'unlock_code';
    const  LOCATION_SUPPORT = 'location_support';
    const  PICTURE_FILE = 'picture_file';
    const  ITTRICORDER_VER = 'ittricorder_ver';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipient_mobile_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recipient_mobile_index', 'timeout_time'], 'integer'],
            [['location_support'], 'boolean'],
            [['server', 'backup_server', 'unlock_code'], 'string', 'max' => 64],
            [['picture_file'], 'string', 'max' => 512],
            [['ittricorder_ver'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'index' => 'Index',
            'recipient_mobile_index' => 'Recipient Mobile Index',
            'server' => 'Server',
            'backup_server' => 'Backup Server',
            'timeout_time' => 'Timeout Time',
            'unlock_code' => 'Unlock Code',
            'location_support' => 'Location Support',
            'picture_file' => 'Picture File',
            'ittricorder_ver' => 'Ittricorder Ver',
        ];
    }
}
