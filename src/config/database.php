<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('CONSUMERWS_DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('CONSUMERWS_DB_HOST'),
            'port' => env('CONSUMERWS_DB_PORT'),
            'database' => env('CONSUMERWS_DB_DATABASE'),
            'username' => env('CONSUMERWS_DB_USERNAME'),
            'password' => env('CONSUMERWS_DB_PASSWORD'),
            'unix_socket' => env('CONSUMERWS_DB_SOCKET', config('constants.db.socket')),
            'charset' => env('CONSUMERWS_DB_CHARSET', config('constants.db.charset')),
            'collation' => env('CONSUMERWS_DB_COLLATION', config('constants.db.collation')),
            'prefix' => env('CONSUMERWS_DB_PREFIX', config('constants.db.prefix')),
            'strict' => env('CONSUMERWS_STRICT_MODE', config('constants.db.strict')),
            'engine' => env('CONSUMERWS_DB_ENGINE', config('constants.db.engine')),
            'timezone' => env('CONSUMERWS_DB_TIMEZONE', config('constants.db.timezone')),
        ],

        'mysql_test' => [
            'driver' => 'mysql',
            'host' => env('CONSUMERWS_TEST_DB_HOST'),
            'port' => env('CONSUMERWS_TEST_DB_PORT'),
            'database' => env('CONSUMERWS_TEST_DB_DATABASE'),
            'username' => env('CONSUMERWS_TEST_DB_USERNAME'),
            'password' => env('CONSUMERWS_TEST_DB_PASSWORD'),
            'unix_socket' => env('CONSUMERWS_TEST_DB_SOCKET', config('constants.db.socket')),
            'charset' => env('CONSUMERWS_TEST_DB_CHARSET', config('constants.db.charset')),
            'collation' => env('CONSUMERWS_TEST_DB_COLLATION', config('constants.db.collation')),
            'prefix' => env('CONSUMERWS_TEST_DB_PREFIX', config('constants.db.prefix')),
            'strict' => env('CONSUMERWS_TEST_STRICT_MODE', config('constants.db.strict')),
            'engine' => env('CONSUMERWS_TEST_DB_ENGINE', config('constants.db.engine')),
            'timezone' => env('CONSUMERWS_TEST_DB_TIMEZONE', config('constants.db.timezone')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',
];
