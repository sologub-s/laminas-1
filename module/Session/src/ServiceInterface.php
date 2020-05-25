<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 17:25
 */

namespace Session;

use Laminas\ServiceManager\ServiceManager;

/**
 * Interface ServiceInterface
 * @package Session
 */
interface ServiceInterface
{
    /**
     * @return bool
     */
    public function isAdminLogged(): bool;

    /**
     * @param string $password
     * @return bool
     */
    public function adminLogin(string $password): bool;

    /**
     * @return void
     */
    public function adminLogout(): void;

    /**
     * @return array
     */
    public function getAdmin(): array;
}