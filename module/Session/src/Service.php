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
     * @return bool
     */
    public function isAdminLogged(): bool
    {
        return \App::getSessionContainer()->admin['isLogged'];
    }

    /**
     * @return array
     */
    public function getAdmin(): array
    {
        return \App::getSessionContainer()->admin;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function adminLogin(string $password): bool
    {
        if ($password !== \App::getConfig()['modules']['backend']['adminPassword']) {
            return false;
        }

        \App::getSessionContainer()->admin['isLogged'] = true;
        \App::getSessionContainer()->admin['loggedAt'] = time();

        return true;
    }

    /**
     * @return void
     */
    public function adminLogout(): void
    {
        \App::getSessionContainer()->admin['isLogged'] = false;
        \App::getSessionContainer()->admin['loggedAt'] = null;
    }

}