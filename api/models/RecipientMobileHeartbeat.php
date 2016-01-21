<?php

namespace api\models;

use api\components\Utils;
use api\modules\v4\ApiParams;
use api\modules\v4\Validation;
use Yii;
use yii\db\ActiveRecord;
use api\models\RecipientMobileLogin;

/**
 * This is the model class for table "recipient_mobile_heartbeat".
 *
 * @property integer $index
 * @property integer $recipient_mobile_index
 * @property string $auth_key
 * @property string $device_id
 * @property string $heartbeat_time
 */
class RecipientMobileHeartbeat extends ActiveRecord
{
    const INDEX ='index';
    const RECIPIENT_MOBILE_INDEX ='recipient_mobile_index';
    const AUTH_KEY ='auth_key';
    const DEVICE_ID ='device_id';
    const HEARTBEAT_TIME ='heartbeat_time';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipient_mobile_heartbeat';
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recipient_mobile_index'], 'integer'],
            [['heartbeat_time'], 'safe'],
            [['auth_key', 'device_id'], 'string', 'max' => 256]
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
            'auth_key' => 'Auth Key',
            'device_id' => 'Device ID',
            'heartbeat_time' => 'Heartbeat Time',
        ];
    }

    /**
     * Returns the fully qualified parent class name.
     *
     * @return string
     */
    public static function extendsFrom()
    {
        return RecipientMobileLogin::className();
    }

    public function setNewLoginSession($model)
    {
        $this->auth_key = $this->setNewAuthKey();
        $this->device_id = $model->device_id;
        $this->heartbeat_time = Utils::getCurrentTime();
        $this->recipient_mobile_index = $model->index;
        if(!$this->save())
        {
            $this->addError(self::AUTH_KEY, $error = "c'not save new session");
        }
        return $this;
    }

    public static function setNewSession($model)
    {

    }
    private function setNewAuthKey()
    {
        $bytes = openssl_random_pseudo_bytes(16, $cstrong);
        $hex   = bin2hex($bytes);
        $this->auth_key = $hex;
        if($this->save())
        {
            return $this->auth_key;
        }
        else
        {
            $this->addError(self::AUTH_KEY, $error = "c'not save new auth key");
            return false;
        }
    }



    private function getUserByDeviceId($device_id)
    {
        return self::findOne([self::DEVICE_ID => $device_id]);
    }
}
