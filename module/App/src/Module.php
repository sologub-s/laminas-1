<?php

declare(strict_types=1);

namespace App;

use Laminas\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        \App::setServiceManager($e->getApplication()->getServiceManager());
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
        //return [];
    }
}
