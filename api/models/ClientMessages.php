<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "client_messages".
 *
 * @property integer $id
 * @property string $sender_client
 * @property string $sender_node
 * @property string $alert_initiator
 * @property integer $alert_type
 * @property integer $alert_sevirity
 * @property string $recipient_info
 * @property string $recv_at
 * @property string $send_at
 * @property string $short_msg_text
 * @property string $long_msg_text
 * @property integer $msg_code
 * @property string $encoding_method
 * @property integer $sender_timezone
 * @property string $send_logic_code
 * @property integer $correlation_rule_id
 * @property integer $status
 * @property integer $alert_priority
 * @property string $msg_params
 * @property string $handle_date
 * @property string $src_encoding
 * @property boolean $is_correlation
 * @property boolean $is_error
 * @property boolean $is_sent
 * @property boolean $is_ack
 * @property boolean $is_rejected
 * @property boolean $is_ack_timeout
 * @property boolean $is_canceled
 * @property string $correlation_code
 * @property string $additional_data
 * @property integer $expiration_timeout
 * @property string $ticket_data
 */
class ClientMessages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'alert_type', 'alert_sevirity', 'msg_code', 'sender_timezone', 'correlation_rule_id', 'status', 'alert_priority', 'expiration_timeout'], 'integer'],
            [['recv_at', 'send_at', 'handle_date'], 'safe'],
            [['is_correlation', 'is_error', 'is_sent', 'is_ack', 'is_rejected', 'is_ack_timeout', 'is_canceled'], 'boolean'],
            [['sender_client'], 'string', 'max' => 8],
            [['sender_node'], 'string', 'max' => 512],
            [['alert_initiator', 'recipient_info', 'additional_data'], 'string', 'max' => 1024],
            [['short_msg_text'], 'string', 'max' => 8750],
            [['long_msg_text'], 'string', 'max' => 14350],
            [['encoding_method', 'src_encoding'], 'string', 'max' => 16],
            [['send_logic_code', 'correlation_code'], 'string', 'max' => 40],
            [['msg_params'], 'string', 'max' => 7200],
            [['ticket_data'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_client' => 'Sender Client',
            'sender_node' => 'Sender Node',
            'alert_initiator' => 'Alert Initiator',
            'alert_type' => 'Alert Type',
            'alert_sevirity' => 'Alert Sevirity',
            'recipient_info' => 'Recipient Info',
            'recv_at' => 'Recv At',
            'send_at' => 'Send At',
            'short_msg_text' => 'Short Msg Text',
            'long_msg_text' => 'Long Msg Text',
            'msg_code' => 'Msg Code',
            'encoding_method' => 'Encoding Method',
            'sender_timezone' => 'Sender Timezone',
            'send_logic_code' => 'Send Logic Code',
            'correlation_rule_id' => 'Correlation Rule ID',
            'status' => 'Status',
            'alert_priority' => 'Alert Priority',
            'msg_params' => 'Msg Params',
            'handle_date' => 'Handle Date',
            'src_encoding' => 'Src Encoding',
            'is_correlation' => 'Is Correlation',
            'is_error' => 'Is Error',
            'is_sent' => 'Is Sent',
            'is_ack' => 'Is Ack',
            'is_rejected' => 'Is Rejected',
            'is_ack_timeout' => 'Is Ack Timeout',
            'is_canceled' => 'Is Canceled',
            'correlation_code' => 'Correlation Code',
            'additional_data' => 'Additional Data',
            'expiration_timeout' => 'Expiration Timeout',
            'ticket_data' => 'Ticket Data',
        ];
    }
}
