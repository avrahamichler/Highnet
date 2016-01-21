<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/7/2016
 * Time: 6:22 PM
 */

namespace api\controllers;


use api\components\Utils;
use api\components\Validation;
use api\ApiParams;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use api\models\Recipients;
use api\models\Groups;


class RecipientsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-recipients-list' => ['post']
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


    public function actionGetRecipientsList()
    {
        $params = ApiParams::getPostJsonParams();

        $required_params = ['deviceID', 'authKey'];

        $params_to_check = ['authKey'];

        $model = Validation::checkUserParams($params, $required_params, $params_to_check);

        if($model->errors)
        {
            Utils::echoErrorResponse($model->errors);
        }
        else
        {
            $recipiens = Recipients::find()->asArray()->all();
            $groups = Groups::find()->select('code')->asArray()->all();
            $groups_members = $this->getGroupsMembers($groups);
            Utils::echoSuccessResponse($groups_members);
        }
    }

    private function getGroupsMembers($groups)
    {
        $groups_members = [];
        foreach($groups as $group)
        {
//            var_dump($group);
//            if($group['code'] == "all.grp")
            $members = Yii::$app->db->createCommand("SELECT * FROM get_group_recipient('".$group['code']."')")->queryAll();
            $groups_members[$group['code']] = $members;
        }
        return $groups_members;
    }


}