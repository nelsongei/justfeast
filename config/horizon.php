<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is null, Horizon will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    'domain' => env('HORIZON_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that isn't exposed to users.
    |
    */

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store its
    | meta-data. The name of this connection should correspond to one of
    | the Redis connections defined in your application's database.php.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when running multiple installations of Horizon
    | on the same server, or to avoid key collisions on a shared Redis.
    |
    */

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'
    ),

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option configures the maximum number of seconds a job may wait
    | in a queue before emitting a warning notification.
    |
    */

    'waits' => [
        'redis:default'   => 30,
        'redis:high'      => 10,
        'redis:broadcast' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings for your application.
    | Horizon allows you to configure workers per environment and queue
    | with dynamic auto-scaling rules during concert peak burst windows.
    |
    */

    'defaults' => [
        'supervisor-stadium-default' => [
            'connection' => 'redis',
            'queue' => ['high', 'default', 'broadcast'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'minProcesses' => 5,
            'maxProcesses' => 20,
            'balanceMaxShift' => 3,
            'balanceCooldown' => 3,
            'tries' => 3,
            'timeout' => 60,
            'memory' => 128,
        ],
        'supervisor-broadcast' => [
            'connection' => 'redis',
            'queue' => ['broadcast'],
            'balance' => 'simple',
            'minProcesses' => 3,
            'maxProcesses' => 10,
            'tries' => 1,
            'timeout' => 15,
            'memory' => 64,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-stadium-default' => [
                'minProcesses' => 10,
                'maxProcesses' => 50,
                'balance' => 'auto',
            ],
            'supervisor-broadcast' => [
                'minProcesses' => 5,
                'maxProcesses' => 20,
            ],
        ],

        'local' => [
            'supervisor-stadium-default' => [
                'minProcesses' => 2,
                'maxProcesses' => 5,
            ],
        ],
    ],
];
