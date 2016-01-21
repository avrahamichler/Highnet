<?php
/**
 * Created by PhpStorm.
 * User: israel kobler
 * Date: 8/5/2015
 * Time: 2:26 PM
 */

namespace api\components;


class ResponseRestApi {

    public static function echoErrorResponse($data){
//        self::setHeader(400);
        echo json_encode(array('error_code' => 400, 'errors' => $data), JSON_PRETTY_PRINT);
    }

    public static function echoSuccessResponse($data){
//        self::setHeader(200);
        echo json_encode(array('data' => $data), JSON_PRETTY_PRINT);
    }

//    public static function echoErrorResponse($function, $data){
//        self::setHeader(400);
//        echo json_encode(['success' => 'false', 'code'=>400,  "error" => $data], JSON_PRETTY_PRINT);
//    }
//
//    public static function echoSuccessResponse($function, $data){
//        self::setHeader(200);
//        echo json_encode(['function'=>$function, 'response'=>array_merge(['success' => 'true', 'code'=>200], $data)], JSON_PRETTY_PRINT);
//    }

    private static function setHeader($status)
    {

        $status_header = 'HTTP/1.1 ' . $status . ' ' . self::_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Ravtech <ravtech.co.il>");
    }

    private static  function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}