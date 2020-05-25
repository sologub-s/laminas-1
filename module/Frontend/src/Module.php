<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 21:38
 */

namespace Frontend;

use Laminas\Mvc\MvcEvent;

/**
 * Class Module
 * @package Frontend
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