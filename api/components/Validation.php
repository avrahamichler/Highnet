<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 12/28/2015
 * Time: 2:22 PM
 */

namespace api\components;




use api\models\ClientMessages;
//use api\models\Highnetusers;
use api\models\RecipientMobileInfo;
use api\models\RecipientMobileLogin;
use api\models\RecipientMobileSetting;

class Validation
{

//    const DEVICE_ID = "Unauthorized device";
    const AUTH_KEY = "Bad authKey";
    const USER_PASSWORD = "Bad username or password";
    const PHONE_NUMBER = "Unauthorized number";
    const USER_LOGIN = "this user name not exist";


    private static function checkCorrectParam($model, $params)
    {
        foreach($params as $key=>$value)
        {
            $func_name = 'check'.$key;
            $constname = self::camelCaseToUnderscore($key);

            if(!$model->$func_name($value))
            {
                $model->addErrors([$key => (string)constant('self::'.$constname)]);
                return $model;
            }
        }
        return $model;

    }


    public static function checkUserParams($params, $required_params, $params_to_check)
    {
        $missing_parameters = self::allTheRequiredParametersAre($params, $required_params);

        $model = new RecipientMobileLogin();

        if($missing_parameters)
        {
            $model->addError("this parameters are missing " ,$error = implode(", ",$missing_parameters));
            return $model;
        }
        else
        {

            $new_model = RecipientMobileLogin::getUserByDeviceID($params['deviceID']);

            if(!$new_model)
            {
//                var_dump($model->device_id);
//                $model->addError("deviceID", $error = "Unauthorized device");
//                return $model;
                if(isset($params['firstTime']) && $params['firstTime'])
                {
                    $new_model = RecipientMobileLogin::getUserByCode($params['userLogin']);
                    if($new_model)
                    {
                        $new_model->setDeviceId($params['deviceID']);
                    }
                }
                else
                {
                    $model->addError("deviceID", $error = "Unauthorized device");
                    return $model;
                }
            }
            else
            {
                $model = $new_model;

                $params_to_check = self::paramsToCheck($params, $params_to_check);

                $model = self::checkCorrectParam($model, $params_to_check);

                return $model;
            }
        }
    }


    public static function allTheRequiredParametersAre($parms, $required_parm)
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

    public static function checkMessage($cmsgOID)
    {
        $message = ClientMessages::findOne(["oid" => $cmsgOID]);
        if(!$message)
        {
            $message->addError("cmoid", $error = "no clint message with this oid");
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