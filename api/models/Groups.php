<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "groups".
 *
 * @property string $code
 * @property string $name
 * @property boolean $is_active
 * @property string $owner_code
 * @property integer $owner_type
 * @property integer $permission
 * @property string $creator
 * @property boolean $is_local
 * @property string $alt1_group
 * @property string $alt2_group
 * @property string $alt3_group
 * @property string $alt4_group
 * @property string $alt5_group
 */
class Groups extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'is_local'], 'boolean'],
            [['owner_type', 'permission'], 'integer'],
            [['code'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 700],
            [['owner_code', 'creator'], 'string', 'max' => 16],
            [['alt1_group', 'alt2_group', 'alt3_group', 'alt4_group', 'alt5_group'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'is_active' => 'Is Active',
            'owner_code' => 'Owner Code',
            'owner_type' => 'Owner Type',
            'permission' => 'Permission',
            'creator' => 'Creator',
            'is_local' => 'Is Local',
            'alt1_group' => 'Alt1 Group',
            'alt2_group' => 'Alt2 Group',
            'alt3_group' => 'Alt3 Group',
            'alt4_group' => 'Alt4 Group',
            'alt5_group' => 'Alt5 Group',
        ];
    }
}
