<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 25.05.2020
 * Time: 14:40
 */

use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Session\Container as SessionContainer;

/**
 * Class App
 */
abstract class App
{
    /**
     * @var ContainerInterface
     */
    public static $serviceManager;

    /**
     * @param $id
     * @return bool
     */
    public static function has($id)
    {
        return self::$serviceManager->has($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function get($id)
    {
        return self::$serviceManager->get($id);
    }

    /**
     * @return SessionContainer
     */
    public static function getSessionContainer()
    {
        return self::get(SessionContainer::class);
    }

    /**
     * @return array
     */
    public static function getConfig()
    {
        return self::get('config');
    }

    /**
     * @return AdapterInterface
     */
    public static function getDbAdapter()
    {
        return self::get(AdapterInterface::class);
    }

    /**
     * @param ContainerInterface $serviceManager
     */
    public static function setServiceManager(ContainerInterface $serviceManager)
    {
        self::$serviceManager = $serviceManager;
    }
}