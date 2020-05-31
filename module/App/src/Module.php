<?php

declare(strict_types=1);

namespace App;

use Laminas\Mvc\MvcEvent;
use Illuminate\Database\Capsule\Manager as Capsule;

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
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
        //return [];
    }
}
