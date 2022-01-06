<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => 'ses',

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array"
    |
    */

    'mailers' => [
        // 'smtp' => [
        //     'transport' => 'smtp',
        //     'host' => 'alesharide.net',
        //     'port' => '465',
        //     'encryption' => 'ssl',
        //     'username' => 'info@payroll.aleshagroup.com',
        //     'password' => 'Info@123#',
        //     'timeout' => null,
        // ],

        'smtp' => [
            'transport' => 'smtp',
            'host' => 'tls://email-smtp.us-east-1.amazonaws.com',
            'port' => '465',
            'encryption' => 'tls',
            'username' => 'noreply@alesharide.com',
            'password' => '@leshaNoreply2021',
            'timeout' => null,
        ],

        'ses' => [
            'transport' => 'ses',
            'protocol' => 'smtp',
            'smtp_host' => 'tls://email-smtp.ap-south-1.amazonaws.com',
            'smtp_user' => 'noreply@alesharide.com',
            'smtp_pass' => '@leshaNoreply2021',
            'smtp_port' => '587',
            'mailtype' => 'html'
        ],

        

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => '/usr/sbin/sendmail -bs',
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'info@alesharide.com'),
        'name' => env('MAIL_FROM_NAME', 'Alesha Ride'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
