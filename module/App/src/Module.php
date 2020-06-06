<?php

declare(strict_types=1);

namespace App;

use Laminas\Mvc\MvcEvent;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        // Global App
        \App::setServiceManager($e->getApplication()->getServiceManager());

        // Eloquent
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => \App::getConfig()['db']['hostname'],
            'database'  => \App::getConfig()['db']['database'],
            'username'  => \App::getConfig()['db']['username'],
            'password'  => \App::getConfig()['db']['password'],
            'charset'   => \App::getConfig()['db']['charset'],
            'collation' => \App::getConfig()['db']['collation'],
            'prefix'    => '',
        ]);
        // Set the event dispatcher used by Eloquent models... (optional)
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
        //return [];
    }
}
