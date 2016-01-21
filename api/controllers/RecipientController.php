<?php

namespace api\controllers;

use api\components\Utils;
use api\models\RecipientMobileHeartbeat;
use api\models\RecipientMobileInfo;
use api\models\RecipientMobileLogin;
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
                    'a' =>['get'],
                    'user-login' => ['post'],
                    'user-restore-password' => ['post'],
                    'user-status' =>['post'],
                    'change-server-info' => ['post'],
                    'change-profile-info' => ['post']
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
        echo "a";
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
            Utils::echoErrorResponse($model->errors);
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




    public function actionUserRestorePassword()
    {
        $params = ApiParams::getPostJsonParams();

        $model = Validation::AuthorizedUserToResetThePassword($params);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->errors);
        }
        else
        {
            if(!$model->security_question)
            {
                Utils::echoErrorResponse(["error" => "You did not register security question, call support"]);
            }
            else
            {
                $good_answer = $model->checkAnswer($params[RecipientMobileLogin::SECURITY_QUESTION], $params[RecipientMobileLogin::SECURITY_ANSWER]);
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
            Utils::echoErrorResponse($model->errors);
        }
        else
        {
            $model = RecipientMobileLogin::findOne([RecipientMobileLogin::INDEX => $model->recipient_mobile_index]);
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
                            "code" => $model->code,
                            "name" => $model->name,
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

    public function changeServerInfo()
    {
        $params = ApiParams::getPostJsonParams();

        $model = Validation::checkParams($params, ApiParams::CHANGE_SERVER_PARAMS);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->errors);
        }
        else
        {
            $setting = RecipientMobileSetting::findOne(['recipient_mobile_index' => $model->index]);

            $setting->server = $params['serverName'];
            $setting->backup_server = $params['backupServer'];
            $setting->timeout_time = $params['timeOut'];

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

    public function changeProfileInfo()
    {
        $params = ApiParams::getPostJsonParams();

        $required_params = ['userLogin', 'deviceID', 'authKey', 'userName', 'password', 'unlockCode', 'isLocation'];

        $params_to_check = ['userLogin', 'authKey'];

        $model = Validation::checkUserParams($params, $required_params, $params_to_check);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->errors);
        }
        else
        {
            $setting = RecipientMobileSetting::findOne(['recipient_mobile_index' => $model->index]);

            $model->recipient_code = $params['userName'];
            $model->mobile_password = $params['password'];

            $setting->unlock_code = $params['unlockCode'];
            $setting->location_support = $params['isLocation'];

            if(!$setting->save() || !$model->save())
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

    private function userJson($model, $setting)
    {
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
                        "code" => $model->code,
                        "name" => $model->name,
                        "unlockCode" => $setting->unlock_code,
                        "isLocation" => $setting->location_support,
                        "picture" => $setting->picture_file
                    ]
            ];
        return $response;
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
