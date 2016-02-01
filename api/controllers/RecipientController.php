<?php

namespace api\controllers;

use api\components\Utils;
use api\models\RecipientMobileHeartbeat;
use api\models\RecipientMobileInfo;
use api\models\RecipientMobileLogin;
use api\models\Recipients;
use api\modules\ApiParams;
use api\modules\InfoTypes;
use api\modules\Validation;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use api\models\RecipientMobileSetting;





class RecipientController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'a' =>['get', 'post'],
                    'user-login' => ['post'],
                    'user-restore-password' => ['post'],
                    'user-status' =>['post'],
                    'change-server-info' => ['post'],
                    'change-profile-info' => ['post'],
                    'user-logout' => ['post']
                ],
            ]
        ];
    }

    public function beforeAction($event)
    {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (isset($this->actions['*'])) {
            $verbs = $this->actions['*'];
        } else {
            return $event->isValid;
        }
        $verb = Yii::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $verbs);
        if (!in_array($verb, $allowed)) {
            Utils::echoErrorResponse('Method not allowed');
            exit;
        }
        return true;
    }

    public function actionA()
    {
//        $model = new RecipientMobileHeartbeat();
//        $last = $model->find(['device_id' => 1])
//            ->where(['not',['heartbeat_time'=>null]])
//            ->orderBy(['heartbeat_time'=>SORT_DESC])
//            ->asArray()
//            ->one();
//        var_dump($last);
        var_dump($_FILES);
    }

    /*
    * @var $model RecipientMobileLogin
    * @var $user_info RecipientMobileInfo
    */
    public function actionUserLogin()
    {

        $params = ApiParams::getPostJsonParams();

        $model = Validation::TheUserLoginCorrectly($params);

        if($model->errors)
        {
            RecipientMobileInfo::setNewRecipientInfo($model, InfoTypes::BAD_LOGIN, $params);
            Utils::echoErrorResponse($model->getFirstErrors());
        }
        else
        {
            $user_info = RecipientMobileInfo::find()
                ->where([RecipientMobileInfo::RECIPIENT_MOBILE_INDEX => $model->index])
                ->andWhere([RecipientMobileInfo::INFO_TYPE => InfoTypes::LOGIN])
                ->one();

            $last_login = $user_info ? $user_info->occured_at : 0;

            RecipientMobileInfo::setNewRecipientInfo($model, InfoTypes::LOGIN, $params);

            $heartbeat = new RecipientMobileHeartbeat();
            $heartbeat = $heartbeat->setNewLoginSession($model);

            if($heartbeat->errors)
            {
                Utils::echoErrorResponse($heartbeat->errors);
            }
            else
            {
                $response = [ApiParams::LAST_LOGIN=> $last_login, ApiParams::AUTH_KEY => $heartbeat->auth_key];
                Utils::echoSuccessResponse($response);
            }
        }
    }

    public function actionUserLogout()
    {

        $params = ApiParams::getPostJsonParams();

        $model = Validation::checkUserAuthorization($params);

        if($model->errors)
        {
            Utils::echoErrorResponse($model->getFirstErrors());
        }
        else
        {
            RecipientMobileInfo::setNewRecipientInfo($model,InfoTypes::LOGOUT, $params);
            $heartbeat = new RecipientMobileHeartbeat();
            $heartbeat = $heartbeat->logOut($model);
            if($heartbeat->errors)
            {
                Utils::echoErrorResponse($heartbeat->errors);
            }
            else
            {
                 Utils::echoSuccessResponse("logout");
            }
        }
    }




    public function actionUserRestorePassword()
    {
        $params = ApiParams::getPostJsonParams();

        $model = Validation::AuthorizedUserToResetThePassword($params);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->getFirstErrors());
        }
        else
        {
            if(!$model->security_question)
            {
                Utils::echoErrorResponse(["error" => "You did not register security question, call support"]);
            }
            else
            {
                $good_answer = $model->checkAnswer($params[ApiParams::SECURITY_QUESTION], $params[ApiParams::SECURITY_QUESTION_ANSWER]);
                if(!$good_answer)
                {
                    Utils::echoErrorResponse(["error" =>"Wrong security question or answer"]);
                }
                else
                {
                    RecipientMobileInfo::setNewRecipientInfo($model, InfoTypes::RESTORE_PASSWORD, $params);
                    Utils::echoSuccessResponse("New Password set");
                }
            }
        }
    }



    public function actionUserStatus()
    {
        $params = ApiParams::getPostJsonParams();

        $model = Validation::checkUserAuthorization($params);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->getFirstErrors());
        }
        else
        {
            $model = RecipientMobileLogin::findOne([RecipientMobileLogin::INDEX => $model->recipient_mobile_index]);
            $name = $this->getRecipientName($model);
            $setting = RecipientMobileSetting::findOne([RecipientMobileSetting::RECIPIENT_MOBILE_INDEX => $model->index]);
            $response =
                [
                    "server" =>
                        [
                            "main" => $setting->server,
                            "backup" => $setting->backup_server,
                            "timeout" => $setting->timeout_time
                        ],
                    "profile" =>
                        [
                            "code" => $model->recipient_code,
                            "name" => $name,
                            "unlockCode" => $setting->unlock_code,
                            "isLocation" => $setting->location_support,
                            "picture" => $setting->picture_file
                        ],
                    "alerts" => "",
                    "tickets" => "",
                    "dutyInfo"=>
                        [

                        ],
                    "message" => ""
                ];

            Utils::echoSuccessResponse($response);
        }
    }

    public function actionChangeServerInfo()
    {
        $params = ApiParams::getPostJsonParams();

        $model = Validation::checkParams($params, ApiParams::$CHANGE_SERVER_PARAMS);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->getFirstErrors());
        }
        else
        {
            $model = RecipientMobileLogin::findOne([RecipientMobileLogin::INDEX => $model->recipient_mobile_index]);
            $setting = RecipientMobileSetting::findOne([RecipientMobileSetting::RECIPIENT_MOBILE_INDEX => $model->index]);

            $setting->server = $params[ApiParams::SERVER_NAME];
            $setting->backup_server = $params[ApiParams::BACKUP_SERVER];
            $setting->timeout_time = $params[ApiParams::TIME_OUT];

            if(!$setting->save())
            {
                Utils::echoErrorResponse("something went wrong with the server, please try again later");
            }
            else
            {
                $response = $this->userJson($model, $setting);

                Utils::echoSuccessResponse($response);
            }
        }
    }

    public function actionChangeProfileInfo()
    {
        $params = ApiParams::getPostJsonParams();

        $model = Validation::checkParams($params, ApiParams::$CHANGE_PROFILE_PARAMS);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->getFirstErrors());
        }
        else
        {
            $model = RecipientMobileLogin::findOne([RecipientMobileLogin::INDEX => $model->recipient_mobile_index]);
            $setting = RecipientMobileSetting::findOne([RecipientMobileSetting::RECIPIENT_MOBILE_INDEX => $model->index]);
            $recipient = Recipients::find()
                ->where(['code' => $model->recipient_code])
                ->one();

            $recipient->name = $params[ApiParams::USER_NAME];

            $model->recipient_code = $params[ApiParams::USER_CODE];
            $model->mobile_password = $params[ApiParams::USER_PASSWORD];

            $setting->unlock_code = $params[ApiParams::UNLOCK_CODE];
            $setting->location_support = $params[ApiParams::IS_LOCATION];

            if(!$setting->save() || !$model->save() || !$recipient->save())
            {
                Utils::echoErrorResponse("something went wrong with the server, please try again later");
            }
            else
            {
                $response = $this->userJson($model, $setting);

                Utils::echoSuccessResponse($response);
            }
        }
    }

    public function actionSaveUserPicture()
    {

        if(!isset($_FILES))
        {
            Utils::echoErrorResponse(['file' =>"not send any file"]);
        }
        else
        {

        }
    }

    private function userJson($model, $setting)
    {
        $name = $this->getRecipientName($model);
        $response =
            [
                "server" =>
                    [
                        "main" => $setting->server,
                        "backup" => $setting->backup_server,
                        "timeout" => $setting->timeout_time
                    ],
                "profile" =>
                    [
                        "code" => $model->recipient_code,
                        "name" => $name,
                        "unlockCode" => $setting->unlock_code,
                        "isLocation" => $setting->location_support,
                        "picture" => $setting->picture_file
                    ]
            ];
        return $response;
    }

    public function getRecipientName($model)
    {
        $recipient = Recipients::find()
            ->where(['code' => $model->recipient_code])
            ->one();
        return $recipient->name;
    }



//    private static function firstTimeConnection($params)
//    {
//        $missing_parameters = Validation::allTheRequiredParametersAre($params, ['deviceID', 'userLogin']);
//
//        if($missing_parameters)
//        {
//            return ["this parameters are missing" => $missing_parameters];
//        }
//        else
//        {
//            if(!$model = Highnetusers::findOne(['code' =>$params['userLogin']]))
//            {
//                return "Unauthorized number";
//            }
//            else
//            {
//                if(!$model->checkDeviceID($params['deviceID']))
//                {
//                    if($model->firstTimeConnection())
//                    {
//                        $model->device_id = $params['deviceID'];
//                        try
//                        {
//                            $model->save();
//                        }
//                        catch (Exception $e)
//                        {
//                           return $e->getMessage();
//                        }
//                    }
//                    else
//                    {
//                        return "Unauthorized device";
//                    }
//
//                }
//                else
//                {
//                    return null;
//                }
//            }
//        }
//
//    }
}
