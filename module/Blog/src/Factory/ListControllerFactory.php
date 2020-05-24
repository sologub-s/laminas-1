<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 22:00
 */

namespace Blog\Factory;

use Blog\Controller\ListController;
use Blog\Model\PostRepositoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container as SessionContainer;

class ListControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return ListController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //return new SomeServiceObject($container->get('Application\Db\ReadOnlyAdapter'));
        // $dbAdapter = $container->get(AdapterInterface::class); // Database adapter for Sql()
        // $config = $container->get('config'); // a plain config array =-)
        return new ListController(
            $container->get(PostRepositoryInterface::class)
            /*, $container->get(AdapterInterface::class)*/
            , $container->get(SessionContainer::class)
            , $container
        );
    }
}