<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property integer $client_message_oid
 * @property string $od_code
 * @property string $recipient_data
 * @property integer $priority
 * @property integer $status
 * @property string $req_date
 * @property string $recv_date
 * @property string $sent_date
 * @property integer $retries
 * @property string $sent_od
 * @property string $sent_device
 * @property integer $sent_protocol
 * @property string $handle_date
 * @property string $recipient_code
 * @property string $correlation_code
 * @property string $escalation_info
 * @property string $msg_id
 * @property string $last_error
 * @property string $msg_text_copy
 * @property string $sr_info
 * @property string $sla_info
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_message_oid', 'priority', 'status', 'retries', 'sent_protocol'], 'integer'],
            [['req_date', 'recv_date', 'sent_date', 'handle_date'], 'safe'],
            [['od_code', 'sent_od'], 'string', 'max' => 16],
            [['recipient_data'], 'string', 'max' => 1024],
            [['sent_device'], 'string', 'max' => 100],
            [['recipient_code', 'correlation_code'], 'string', 'max' => 40],
            [['escalation_info'], 'string', 'max' => 30],
            [['msg_id'], 'string', 'max' => 2048],
            [['last_error', 'sla_info'], 'string', 'max' => 256],
            [['msg_text_copy'], 'string', 'max' => 8750],
            [['sr_info'], 'string', 'max' => 600]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_message_oid' => 'Client Message Oid',
            'od_code' => 'Od Code',
            'recipient_data' => 'Recipient Data',
            'priority' => 'Priority',
            'status' => 'Status',
            'req_date' => 'Req Date',
            'recv_date' => 'Recv Date',
            'sent_date' => 'Sent Date',
            'retries' => 'Retries',
            'sent_od' => 'Sent Od',
            'sent_device' => 'Sent Device',
            'sent_protocol' => 'Sent Protocol',
            'handle_date' => 'Handle Date',
            'recipient_code' => 'Recipient Code',
            'correlation_code' => 'Correlation Code',
            'escalation_info' => 'Escalation Info',
            'msg_id' => 'Msg ID',
            'last_error' => 'Last Error',
            'msg_text_copy' => 'Msg Text Copy',
            'sr_info' => 'Sr Info',
            'sla_info' => 'Sla Info',
        ];
    }
}
