<?php

namespace api\models;

use api\components\Utils;
use api\modules\ApiParams;
use SplEnum;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "recipient_mobile_info".
 *
 * @property integer $index
 * @property integer $recipient_mobile_index
 * @property integer $info_type
 * @property string $occured_at
 * @property double $pos_lat
 * @property double $pos_lng
 * @property string $note
 */
class RecipientMobileInfo extends \yii\db\ActiveRecord
{

    const INDEX = 'index';
    const RECIPIENT_MOBILE_INDEX ='recipient_mobile_index';
    const INFO_TYPE = 'info_type';
    const OCCURED_AT ='occured_at';
    const POS_LAT ='pos_lat';
    const POS_LNG ='pos_lng';
    const NOTE ='note';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipient_mobile_info';
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recipient_mobile_index', 'info_type'], 'integer'],
            [['occured_at'], 'safe'],
            [['pos_lat', 'pos_lng'], 'number'],
            [['note'], 'string', 'max' => 512]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'index' => 'Index',
            'recipient_mobile_index' => 'Recipient Mobile Index',
            'info_type' => 'Info Type',
            'occured_at' => 'Occured At',
            'pos_lat' => 'Pos Lat',
            'pos_lng' => 'Pos Lng',
            'note' => 'Note',
        ];
    }


    public static function setNewRecipientInfo($model, $info_type, $params)
    {
        try
        {
            $user_info = new RecipientMobileInfo();
            $user_info->recipient_mobile_index = $model->index;
            $user_info->info_type = $info_type;
            $user_info->occured_at = Utils::getCurrentTime();
            if(ApiParams::checkLocationParams($params))
            {
                $user_info->pos_lat = $params[ApiParams::POS_LNG];
                $user_info->pos_lng = $params[ApiParams::POS_LAT];
            }
            elseif(ApiParams::checkIfThereAreNote($params))
            {
                $user_info->note = $params[ApiParams::NOTE];
            }
            return $user_info->save();
        }
        catch(\Exception $e)
        {
            return $e;
        }
    }


}



