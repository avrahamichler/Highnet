<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/4/2016
 * Time: 4:18 PM
 */

namespace api\controllers;


use api\components\Utils;

use api\modules\ApiParams;
use api\modules\Validation;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class AlertController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-alert-info' => ['post'],
                    'ack-alert' => ['post'],
                    'view-alert' => ['post']
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

    public function actionGetAlertInfo()
    {
        $params = ApiParams::getPostJsonParams();

        $message = Validation::checkParamsAndGetMessage($params, ApiParams::$GET_ALERT_INFO_PARAMS);

        if ($message->errors)
        {
            Utils::echoErrorResponse($message->getFirstErrors());
        }

        else
        {
            Utils::echoSuccessResponse("ssss");
        }
    }

    public function actionAckAlert()
    {
        $params = ApiParams::getPostJsonParams();

        $message = Validation::checkParamsAndGetMessage($params, ApiParams::$ACK_ALERT_PARAMS);

        if($message->errors)
        {
            Utils::echoErrorResponse($message->getFirstErrors());
        }
        else
        {
            $ack = $params['answer'] == "ack"? true : false;
            $message->is_ack = $ack;
            if(!$message->save())
            {
                Utils::echoErrorResponse("something went wrong with the server, please try again later");
            }
            else
            {
                Utils::echoSuccessResponse(["OK"]);
            }
        }
    }

    public function actionViewAlert()
    {
        $params = ApiParams::getPostJsonParams();

        $message = Validation::checkParamsAndGetMessage($params, ApiParams::$ACK_ALERT_PARAMS);

        if($message->errors)
        {
            Utils::echoErrorResponse($message->getFirstErrors());
        }
        else
        {
            //TODO ......
        }
    }

}

