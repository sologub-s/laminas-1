<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 21:38
 */

namespace Backend;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container as SessionContainer;

/**
 * Class Module
 * @package Backend
 */
class Module
{

    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getParam('application');
    }

    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}