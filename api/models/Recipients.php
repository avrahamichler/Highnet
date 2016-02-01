<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "recipients".
 *
 * @property int $oid
 * @property string $code
 * @property string $name
 * @property boolean $is_use_logic
 * @property string $logic_code
 * @property boolean $is_timeoff
 * @property string $timeoff_start
 * @property string $timeoff_end
 * @property string $timeoff_alt_recipient_info
 * @property boolean $is_active
 * @property string $owner_code
 * @property integer $owner_type
 * @property integer $permission
 * @property string $creator
 * @property boolean $is_local
 * @property integer $timezone
 * @property string $alt1_recipient_info
 * @property string $alt2_recipient_info
 * @property string $alt3_recipient_info
 * @property string $alt4_recipient_info
 * @property string $alt5_recipient_info
 * @property integer $behavior_on_low
 * @property integer $behavior_on_normal
 * @property integer $behavior_on_high
 * @property integer $behavior_on_urgent
 * @property boolean $is_sync_with_ldap
 * @property boolean $is_ldap_auth
 * @property string $ldap_src_code
 * @property string $ldap_name
 * @property string $recipient_pass
 */
class Recipients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipients';
    }

    /**
     * @return string
     */
    public static function primaryKey()
    {
        return ['oid'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_use_logic', 'is_timeoff', 'is_active', 'is_local', 'is_sync_with_ldap', 'is_ldap_auth'], 'boolean'],
            [['timeoff_start', 'timeoff_end'], 'safe'],
            [['owner_type', 'permission', 'timezone', 'behavior_on_low', 'behavior_on_normal', 'behavior_on_high', 'behavior_on_urgent'], 'integer'],
            [['code'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 700],
            [['logic_code', 'owner_code', 'creator'], 'string', 'max' => 16],
            [['timeoff_alt_recipient_info'], 'string', 'max' => 255],
            [['alt1_recipient_info', 'alt2_recipient_info', 'alt3_recipient_info', 'alt4_recipient_info', 'alt5_recipient_info'], 'string', 'max' => 100],
            [['ldap_src_code'], 'string', 'max' => 64],
            [['ldap_name', 'recipient_pass'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'oid' =>'Oid',
            'code' => 'Code',
            'name' => 'Name',
            'is_use_logic' => 'Is Use Logic',
            'logic_code' => 'Logic Code',
            'is_timeoff' => 'Is Timeoff',
            'timeoff_start' => 'Timeoff Start',
            'timeoff_end' => 'Timeoff End',
            'timeoff_alt_recipient_info' => 'Timeoff Alt Recipient Info',
            'is_active' => 'Is Active',
            'owner_code' => 'Owner Code',
            'owner_type' => 'Owner Type',
            'permission' => 'Permission',
            'creator' => 'Creator',
            'is_local' => 'Is Local',
            'timezone' => 'Timezone',
            'alt1_recipient_info' => 'Alt1 Recipient Info',
            'alt2_recipient_info' => 'Alt2 Recipient Info',
            'alt3_recipient_info' => 'Alt3 Recipient Info',
            'alt4_recipient_info' => 'Alt4 Recipient Info',
            'alt5_recipient_info' => 'Alt5 Recipient Info',
            'behavior_on_low' => 'Behavior On Low',
            'behavior_on_normal' => 'Behavior On Normal',
            'behavior_on_high' => 'Behavior On High',
            'behavior_on_urgent' => 'Behavior On Urgent',
            'is_sync_with_ldap' => 'Is Sync With Ldap',
            'is_ldap_auth' => 'Is Ldap Auth',
            'ldap_src_code' => 'Ldap Src Code',
            'ldap_name' => 'Ldap Name',
            'recipient_pass' => 'Recipient Pass',
        ];
    }


}
