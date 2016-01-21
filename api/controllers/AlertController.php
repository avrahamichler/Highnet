<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/4/2016
 * Time: 4:18 PM
 */

namespace api\controllers;


use api\components\Utils;
use api\components\Validation;
use api\ApiParams;
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

        $required_params = ['deviceID', 'authKey','cmsgOID'];

        $params_to_check = ['authKey'];

        $model = Validation::checkUserParams($params, $required_params, $params_to_check);

        if ($model->errors)
        {
            Utils::echoErrorResponse($model->errors);
        }
        else
        {
            $query = new Query;

            $query->select("oid as cmsgOID, id as msgOID, sender_client, sender_node, alert_initiator, alert_type,
                                   alert_sevirity as severity, recipient_info, recv_at, send_at, short_msg_text,
                                   long_msg_text, msg_code, encoding_method, sender_timezone, send_logic_code,
                                   correlation_rule_id, status, alert_priority, msg_params, handle_date,
                                   src_encoding, is_correlation, is_error, is_sent, is_ack, is_rejected,
                                   is_ack_timeout, is_canceled, correlation_code, additional_data,
                                   expiration_timeout, ticket_data")
                ->from('public.client_messages')
                ->where(["oid" => $params['cmsgOID']]);

            $response = $query->one();

            if(!$response)
            {
                Utils::echoErrorResponse("no clint message with this oid");
            }
            else
            {
                Utils::echoSuccessResponse($response);

            }
        }
    }

    public function actionAckAlert()
    {
        $params = ApiParams::getPostJsonParams();

        $required_params = ['deviceID', 'authKey','cmsgOID', 'answer'];

        $params_to_check = ['authKey'];

        $model = Validation::checkUserParams($params, $required_params, $params_to_check);

        $message = Validation::checkMessage($params['cmsgOID']);

        if($model->errors)
        {
            Utils::echoErrorResponse($model->errors);
        }
        elseif($message->errors)
        {
            Utils::echoErrorResponse($message->errors);
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

        $required_params = ['deviceID', 'authKey','cmsgOID', 'answer'];

        $params_to_check = ['authKey'];

        $model = Validation::checkUserParams($params, $required_params, $params_to_check);

        $message = Validation::checkMessage($params['cmsgOID']);

        if($model->errors)
        {
            Utils::echoErrorResponse($model->errors);
        }
        elseif($message->errors)
        {
            Utils::echoErrorResponse($message->errors);
        }
        else
        {
            //TODO ......
        }
    }

}

