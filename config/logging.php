<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Handler\SwiftMailerHandler;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

return [

    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'email'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'email' => [
            'driver' => 'monolog',
            'level' => 'error',
            'handler' => SwiftMailerHandler::class,
            'handler_with' => [
                'mailer' => new Swift_Mailer((new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT')))
                    ->setUsername(env('MAIL_USERNAME'))
                    ->setPassword(env('MAIL_PASSWORD'))
                    ->setEncryption(env('MAIL_ENCRYPTION'))),
                'message' => (new Swift_Message('Error Log'))
                    ->setFrom([env('MAIL_FROM_ADDRESS') => env('MAIL_FROM_NAME')])
                    ->setTo([env('MAIL_TO_ADDRESS')]),
            ],
        ],
    ],

];