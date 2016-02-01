<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/12/2016
 * Time: 4:40 PM
 */

namespace api\modules;


use api\components\Utils;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\BaseJson;

class ApiParams
{
    const USER_CODE = 'userLogin';
    const USER_PASSWORD = 'userPassword';
    const DEVICE_ID = 'deviceID';
    const PHONE_NUMBER = 'phoneNumber';
    const SECURITY_QUESTION = 'securityQuestion';
    const SECURITY_QUESTION_ANSWER = 'securityQuestionAnswer';
    const AUTH_KEY = 'authKey';
    const SERVER_NAME = 'serverName';
    const BACKUP_SERVER = 'backupServer';
    const TIME_OUT = 'timeOut';
    const UNLOCK_CODE = 'unlockCode';
    const IS_LOCATION = 'isLocation';
    const POS_LAT = 'pos_lat';
    const POS_LNG = 'pos_lng';
    const NOTE = 'note';
    const LAST_LOGIN = "lastLogin";
    const USER_NAME = 'userName';
    const CMSG_OID = "cmsgOID";
    const ANSWER = "answer";

    public static $REQUIRED_LOGIN_PARAMS = [self::USER_CODE, self::USER_PASSWORD, self::DEVICE_ID];
    public static $REQUIRED_RESTORE_PASSWORD_PARAMS = [self::USER_CODE, self::DEVICE_ID, self::SECURITY_QUESTION, self::SECURITY_QUESTION_ANSWER];
    public static $AUTHORIZATION_PARAMS = [self::DEVICE_ID, self::AUTH_KEY];
    public static $CHANGE_SERVER_PARAMS = [self::USER_CODE, self::DEVICE_ID, self::AUTH_KEY, self::SERVER_NAME, self::BACKUP_SERVER, self::TIME_OUT];
    public static $CHANGE_PROFILE_PARAMS = [self::USER_CODE, self::DEVICE_ID, self::AUTH_KEY, self::USER_NAME, self::USER_PASSWORD, self::UNLOCK_CODE, self::IS_LOCATION];
    public static $SAVE_USER_PICTURE = [self::DEVICE_ID, self::AUTH_KEY];

    public static $GET_ALERT_INFO_PARAMS = [self::DEVICE_ID, self::AUTH_KEY, self::CMSG_OID];
    public static $ACK_ALERT_PARAMS = [self::DEVICE_ID, self::AUTH_KEY, self::CMSG_OID, self::ANSWER];

    public static $HEARTBEAT_PARAMS = [self::DEVICE_ID, self::AUTH_KEY, ];


    private static function allTheRequiredParametersAre($parms, $required_parm)
    {

        $missing_parameters = [];
        foreach($required_parm as $parm)
        {
            if(!array_key_exists($parm, $parms) || empty($parms[$parm]))
            {
                array_push($missing_parameters, $parm);
            }
        }
        return $missing_parameters;
    }

    public static function checkRequiredParams($params, $required_params)
    {
        $missing_parameters = self::allTheRequiredParametersAre($params, $required_params);

        $model = new Model();

        if($missing_parameters)
        {
            $model->addError("this parameters are missing " ,$error = implode(", ",$missing_parameters));
        }
        return $model;
    }

    public static function checkLocationParams($params)
    {
        $required_parm = [self::POS_LAT, self::POS_LNG];
        return !self::allTheRequiredParametersAre($params,$required_parm);
    }

    public static function checkIfThereAreNote($params)
    {
        return isset($params[self::NOTE]) && $params[self::NOTE];
    }

    public static function getPostJsonParams()
    {
        $post = file_get_contents("php://input");
        //decode json post input as php array:
        try
        {
            $params = BaseJson::decode( $post, $asArray = true );
            if(!$params)
                $params=[];
            return $params;
        }
        catch(InvalidParamException $e)
        {
            Utils::echoErrorResponse($e->getMessage());
            die;
        }
    }


}