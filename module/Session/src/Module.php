<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 21:38
 */

namespace Session;

use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\Container as SessionContainer;

/**
 * Class Module
 * @package Session
 */
class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {

        //\App::getSessionContainer()->album = 'I got a new CD with awesome music. session';

        $moduleConfig = \App::getConfig()['modules']['session']; // a plain config array =-)

        \App::getSessionContainer()->admin = array_merge(
            $moduleConfig['admin'],
            \App::getSessionContainer()->admin ?? [],
        );
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}