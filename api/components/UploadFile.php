<?php

namespace api\components;


use Yii;
use yii\helpers\Url;


class UploadFile
{

    public static function saveImage($location, $sourceName, $fileName)
    {
        if (isset($_FILES[$sourceName]))
        {
            $fileName = $fileName.'.jpg' ;
            $path = '/upload/'.$location.'/' ;
            $saveImage = self::SaveFileFromPost($sourceName,Yii::$app->basePath.$path,$fileName,false);
            $link = substr(yii::getAlias('@web'), 0, -4);
            $link = Url::to($link.$path.$fileName, true);
            if ($saveImage == true) {
                return $link;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public static function SaveFileFromPost($keyFile, $path, $fileName, $fileAppend = true)
    {

        if (substr($path, -1) != '/') {
            $path .= '/';
        }
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $file = file_get_contents($_FILES[$keyFile]['tmp_name']);
        if (empty($file)) {
            return false;
        } else {
            return UploadFile::SaveFile($path, $fileName, $file, $fileAppend);

        }
    }

    public static function SaveFile($path, $fileName, $file, $fileAppend)
    {
        if (substr($path, -1) != '/') {
            $path .= '/';
        }
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            //return false ;
        }
        if ($fileAppend) {
            $succesfully = file_put_contents($path . $fileName, $file, FILE_APPEND);

        } else {
            $succesfully = file_put_contents($path . $fileName, $file);
        }
        chmod($path . $fileName, 0777);
        if ($succesfully > 0) {
            return true;
        }
    }

}