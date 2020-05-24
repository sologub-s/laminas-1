<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 17:28
 */

namespace Session;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\Container as SessionContainer;

/**
 * Class Service
 * @package Session
 */
class Service implements ServiceInterface
{
    /**
     * @var SessionContainer
     */
    private $sessionContainer;

    /**
     * @var ServiceManager
     */
    private $container;

    /**
     * Service constructor.
     * @param ServiceManager $container
     */
    public function __construct(ServiceManager $container)
    {
        $zxc = 1;
        $this->sessionContainer = $container->get(SessionContainer::class);
        $this->container = $container;
    }

    /**
     * @return bool
     */
    public function isAdminLogged(): bool
    {
        return $this->sessionContainer->admin['isLogged'];
    }

    public function getAdmin(): array
    {
        return $this->sessionContainer->admin;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function adminLogin(string $password): bool
    {
        if ($password !== $this->container->get('config')['modules']['backend']['adminPassword']) {
            return false;
        }

        $this->sessionContainer->admin['isLogged'] = true;
        $this->sessionContainer->admin['loggedAt'] = time();

        return true;
    }

    /**
     * @return void
     */
    public function adminLogout(): void
    {
        $this->sessionContainer->admin['isLogged'] = false;
        $this->sessionContainer->admin['loggedAt'] = null;
    }

}