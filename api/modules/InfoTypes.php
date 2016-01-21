<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/18/2016
 * Time: 1:12 PM
 */

namespace api\modules;



use abhimanyu\enum\helpers\BaseEnum;

class InfoTypes extends BaseEnum
{

    const LOGIN = 1;
    const LOGOUT = 2;
    const RESTORE_PASSWORD = 3;
    const LOCATION_INFO = 4;
    const BAD_LOGIN = 5;
    const VERSION_UPGRADE = 6;

}