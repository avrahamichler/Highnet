<?php
if( __DIR__ == "C:\\Program Files (x86)\\EasyPHP-Devserver-16.1\\eds-www\\Highnet\\common\\config")
{
    $path = 'pgsql:host=192.168.44.55;port=5432;dbname=sns';
}
else
{
    $path =  'pgsql:host=94.188.161.142;port=5432;dbname=sns';
}

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => $path,
            'username' => 'snspp',
            'password' => 'highnet',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql'=> [
                    'class'=>'yii\db\pgsql\Schema',
                    'defaultSchema' => 'public' //specify your schema here
                ]
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
