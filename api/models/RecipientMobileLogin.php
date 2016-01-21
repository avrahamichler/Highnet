<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "recipient_mobile_login".
 *
 * @property integer $index
 * @property string $recipient_code
 * @property string $device_id
 * @property string $mobile_password
 * @property string $phone_number
 * @property integer $security_question
 * @property string $security_answer
 * @property boolean $is_active
 */
class RecipientMobileLogin extends \yii\db\ActiveRecord
{

    const INDEX ='index';
    const RECIPIENT_CODE ='recipient_code';
    const DEVICE_ID ='device_id';
    const MOBILE_PASSWORD ='mobile_password';
    const PHONE_NUMBER ='phone_number';
    const SECURITY_QUESTION ='security_question';
    const SECURITY_ANSWER ='security_answer';
    const IS_ACTIVE ='is_active';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipient_mobile_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['security_question'], 'integer'],
            [['is_active'], 'boolean'],
            [['recipient_code'], 'string', 'max' => 40],
            [['device_id', 'mobile_password'], 'string', 'max' => 256],
            [['phone_number'], 'string', 'max' => 13],
            [['security_answer'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'index' => 'Index',
            'recipient_code' => 'Recipient Code',
            'device_id' => 'Device ID',
            'mobile_password' => 'Mobile Password',
            'phone_number' => 'Phone Number',
            'security_question' => 'Security Question',
            'security_answer' => 'Security Answer',
            'is_active' => 'Is Active',
        ];
    }
    public function firstTimeConnection()
    {
        return  !$this->device_id;
    }
    /*
     * @return RecipientMobileLogin
     */
    public static function getUserByDeviceID($device_id)
    {
        return self::findOne(["device_id" => $device_id]);
    }
    /*
     * @var $model RecipientMobileLogin
     */
    public static function getUserByCode($userLogin)
    {
        return self::findOne(["recipient_code" => $userLogin]);
    }

    public function checkUserPassword($user_password)
    {
        return $user_password != $this->mobile_password;
    }

    public function checkAuthKey($auth_key)
    {
        return $auth_key == $this->auth_key;
    }

    public function checkUserLogin($user_login)
    {
        return $user_login == $this->recipient_code;
    }

    public function checkPhoneNumber($phone_number)
    {
        return $phone_number == $this->phone_number;
    }

    public function checkDeviceID($dvice_id)
    {
        return $dvice_id == $this->device_id;
    }

    public function setDeviceID($device_id)
    {
        $this->dvice_id = $device_id;
    }

    public function checkAnswer($questions, $answer){
        return $this->security_question == $questions && $this->security_answer == $answer;
    }
}
