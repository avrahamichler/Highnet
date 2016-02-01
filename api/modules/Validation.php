<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 12/28/2015
 * Time: 2:22 PM
 */

namespace api\modules;




use api\models\ClientMessages;
//use api\models\Highnetusers;
use api\models\RecipientMobileHeartbeat;
use api\models\RecipientMobileInfo;
use api\models\RecipientMobileLogin;
use api\models\RecipientMobileSetting;

class Validation
{

    const DEVICE_ID_ERROR_MESSAGE = "Unauthorized device";
    const AUTH_KEY_ERROR_MESSAGE = "Bad authKey";
    const USER_PASSWORD_ERROR_MESSAGE = "Bad password";
    const USER_NAME_ERROR_MESSAGE = "Bad username";
    const PHONE_NUMBER_ERROR_MESSAGE = "Unauthorized number";
    const USER_LOGIN_ERROR_MESSAGE = "this user name not exist";
    const OID_ERROR_MESSAGE ="no clint message with this oid";



    /* @var $model RecipientMobileLogin
     * @return RecipientMobileLogin|null|\yii\db\ActiveRecord|static
     */
    public static function TheUserLoginCorrectly($params)
    {
        $model = ApiParams::checkRequiredParams($params, ApiParams::$REQUIRED_LOGIN_PARAMS);
        if ($model->errors)
        {
            return $model;
        }
        else
        {
            $new_model = RecipientMobileLogin::getUserByDeviceID($params[ApiParams::DEVICE_ID]);
            if(!$new_model)
            {
                $new_model = RecipientMobileLogin::getUserByCode($params[ApiParams::USER_CODE]);
                if($new_model && $new_model->checkUserPassword($params[ApiParams::USER_PASSWORD]))
                {
                    if($new_model->firstTimeConnection())
                    {
                        $new_model->setDeviceId($params[ApiParams::DEVICE_ID]);
                        return $model;
                    }
                    else
                    {
                        $model->addError(RecipientMobileLogin::DEVICE_ID, $error = 'you register with another device');
                        return $model;
                    }

                }
                else
                {
                    $model->addError(RecipientMobileLogin::DEVICE_ID, $error = Validation::DEVICE_ID_ERROR_MESSAGE);

                    return $model;
                }
            }
            $model = $new_model;
            $model = self::checkloginParams($model, $params);
            return $model;

        }
    }

    /* @var $model RecipientMobileLogin */

    public static function checkloginParams($model, $params, $restore = false)
    {
//        if(!$model->checkPhoneNumber($params[ApiParams::PHONE_NUMBER]))
//        {
//            $model->addError($model::PHONE_NUMBER, $error = self::PHONE_NUMBER_ERROR_MESSAGE);
//        }
        if(!$model->checkUserLogin($params[ApiParams::USER_CODE]))
        {
            $model->addError($model::RECIPIENT_CODE, $error = self::USER_NAME_ERROR_MESSAGE);
        }
        elseif(!$restore && !$model->checkUserPassword($params[ApiParams::USER_PASSWORD]))
        {
            $model->addError($model::MOBILE_PASSWORD, $error = self::USER_PASSWORD_ERROR_MESSAGE);
        }
        return $model;
    }


    public static function AuthorizedUserToResetThePassword($params)
    {
        $model = ApiParams::checkRequiredParams($params, ApiParams::$REQUIRED_RESTORE_PASSWORD_PARAMS);
        if ($model->errors)
        {
            return $model;
        }
        else
        {
            $new_model = RecipientMobileLogin::getUserByDeviceID($params[ApiParams::DEVICE_ID]);
            if(!$new_model)
            {
                $model->addError(RecipientMobileLogin::DEVICE_ID, $error = self::DEVICE_ID_ERROR_MESSAGE);
                return $model;
            }
            else
            {
                $model = self::checkloginParams($new_model, $params, true);
            }
            return $model;

        }

    }

    public static function checkUserAuthorization($params)
    {
        $model = ApiParams::checkRequiredParams($params, ApiParams::$AUTHORIZATION_PARAMS);
        if ($model->errors)
        {
            return $model;
        }
        else
        {
            $model = self::thisUserIsLogin($params[ApiParams::DEVICE_ID], $params[ApiParams::AUTH_KEY]);
            return $model;
        }

    }


    private static function thisUserIsLogin($device_id, $auth_key)
    {
        $model = new RecipientMobileHeartbeat();
        if(!$model = $model->getUserByDeviceId($device_id))
        {
            $model->addError(RecipientMobileHeartbeat::DEVICE_ID, $error = self::DEVICE_ID_ERROR_MESSAGE);

        }
        elseif(!$model->checkAuthorization($auth_key))
        {
            $model->addError(RecipientMobileHeartbeat::AUTH_KEY, $error = self::AUTH_KEY_ERROR_MESSAGE);

        }
        return $model;
    }


    public static function checkParams($params, $require_params)
    {
        $model = ApiParams::checkRequiredParams($params, $require_params);
        if($model->errors)
        {
            return $model;
        }
        else
        {
            $model = self::thisUserIsLogin($params[ApiParams::DEVICE_ID], $params[ApiParams::AUTH_KEY]);
            return $model;
        }
    }

    public static function checkParamsAndGetMessage($params, $require_params)
    {
        $model = ApiParams::checkRequiredParams($params, $require_params);
        if($model->errors)
        {
            return $model;
        }
        else
        {
            $model = self::thisUserIsLogin($params[ApiParams::DEVICE_ID], $params[ApiParams::AUTH_KEY]);
            if($model->errors)
            {
                return $model;
            }
            else
            {
                $message = Validation::checkMessage($params['cmsgOID']);
                return $message;
            }
        }
    }




    public static function checkMessage($cmsgOID)
    {
        $message = ClientMessages::findOne(["oid" => $cmsgOID]);
        if(!$message)
        {
            $message->addError("cmoid", $error = self::OID_ERROR_MESSAGE);
        }
        return $message;
    }




    private static function underscoreToCamelCase( $string)
    {
        $string = ucfirst(strtolower($string));
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $string);
    }

    private static function camelCaseToUnderscore($string)
    {
        return strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }



    private static function paramsToCheck($params, $params_to_check)
    {
        $array = [];
        foreach ($params_to_check as $param) {
            $array[$param] = $params[$param];
        }
        return $array;
    }

}