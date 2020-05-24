<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 21:40
 */

namespace Session;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

return [
    'service_manager' => [

        'aliases' => [
            //Model\PostRepositoryInterface::class => Model\PostRepository::class,
            //Model\PostRepositoryInterface::class => Model\LaminasDbSqlRepository::class,
            //Model\PostCommandInterface::class => Model\PostCommand::class,
            //Model\PostCommandInterface::class => Model\LaminasDbSqlCommand::class,
            \Session\ServiceInterface::class => \Session\Service::class,
        ],
        'factories' => [
            //Model\PostRepository::class => InvokableFactory::class, // because factory has no dependencies by itself
            \Session\Service::class => ReflectionBasedAbstractFactory::class, // automatically inject dependencies
        ],
    ],
];