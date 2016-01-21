<?php
/**
 * Created by PhpStorm.
 * User: israel kobler
 * Date: 8/5/2015
 * Time: 2:30 PM
 */

namespace api\components;


use api\models\Highnetusers;
use api\models\SaveFile;
use api\models\UserKlint;



class Utils {

    static function echoErrorResponse($data){
        ResponseRestApi::echoErrorResponse($data);
    }

    static  function echoSuccessResponse($data){
        ResponseRestApi::echoSuccessResponse($data);
    }
//    static function echoErrorResponse($data){
//       ResponseRestApi::echoErrorResponse(substr(debug_backtrace()[1]['function'], 6),$data);
//    }
//
//    static  function echoSuccessResponse($data){
//        ResponseRestApi::echoSuccessResponse(substr(debug_backtrace()[1]['function'], 6), $data);
//    }

    public static function echoSuccessAndErrorsResponse($data , $errors)
    {
        $data['errors'] = $errors ;
        ResponseRestApi::echoSuccessResponse($data);
    }

    static function pushGcmToOne($registration_gcm, $msg){
        $msg = array ( "message" => $msg);
        $gcm = new GcmPush();
        return $gcm->pushToOne($registration_gcm , $msg);
    }

    static function pushGcmToSome($registration_gcm, $msg){
        $msg = array ( "message" => $msg);
        $gcm = new GcmPush();
        return $gcm->pushToSome($registration_gcm , $msg);
    }

    static function array_filter_recursive( array $array, callable $callback = null ) {
        $array = is_callable( $callback ) ? array_filter( $array, $callback ) : array_filter( $array );
        foreach ( $array as &$value ) {
            if ( is_array( $value ) ) {
                $value = call_user_func([__CLASS__,  __FUNCTION__], $value, $callback );
            }
        }

        return $array;
    }

     public static function getUserByDeviceId($params, $key)
    {
        if (isset($params[$key]) and $params[$key]) {
            $isExist = Highnetusers::findOne(['DeviceID'=>$params[$key]]);
            if ($isExist) {
                return $isExist;
            } else {
                return 'error';
            }

        } else {
            return 'error';
        }
    }





    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    public static function saveImage(&$params ,$key, &$model,$type,$fileName ,$specificPath){
        if (isset($params[$key]) && $params[$key]!=null)
        {
            if(isset($_FILES[$params[$key]]))
            {
                $image = new SaveFile($params[$key],$fileName, $specificPath, $type);
                if (!$image->errors && $image->saveFile())
                {
                    $params[$key] = $image->link;
                }
                else
                {
                    $model->addError($key,$image->errors['file']);
                }
            }
            else
            {
                $model->addError($key,'you get this key: "' .$params[$key].'". for image file, bat you not send file in this key');

            }


        }
    }
    public static function getCurrentTime()
    {
        $time = new \DateTime('now', new \DateTimeZone('UTC'));
        return $time->format('Y-m-d H:i:s');
    }

}