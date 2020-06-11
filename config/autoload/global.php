<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

// best place for some kinds of custom configs

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Session;
use App\ViewHelper;

return [
    'db' => [
        'driver' => 'Pdo_Mysql',
        'database' => '', // override
        'username' => '', // override
        'password' => '', // override
        'hostname' => '', // override
        'port' => 3306,
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        /*
        'driver_options' => [
            // Turn off persistent connections
            PDO::ATTR_PERSISTENT => false,
            // Enable exceptions
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Emulate prepared statements
            PDO::ATTR_EMULATE_PREPARES => true,
            // Set default fetch mode to array
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Set character set
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
        ],
        */
    ],



    'session_containers' => [
        Laminas\Session\Container::class,
    ],
    'session_storage' => [
        'type' => Laminas\Session\Storage\SessionArrayStorage::class,
    ],
    'session_config'  => [
        //'gc_maxlifetime' => 7200, // not necessary
        // …
    ],

    'modules' => [
        'session' => [
            'a' => 'A',
            'admin' => [
                'isLogged' => false, // whether admin is logged or not
                'loggedAt' => null, // when the login happened
            ],
        ],
        'backend' => [
            'adminPassword' => '12345',
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'date' => ViewHelper\Date::class,
            'sortableColumnName' => ViewHelper\SortableColumnName::class,
            'getToHidden' => ViewHelper\GetToHidden::class,
        ],
        'factories' => [
            ViewHelper\Date::class => InvokableFactory::class,
            ViewHelper\SortableColumnName::class => InvokableFactory::class,
            ViewHelper\GetToHidden::class => InvokableFactory::class,
        ],
    ],

];
