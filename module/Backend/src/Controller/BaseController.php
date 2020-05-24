<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:55
 */

namespace Backend\Controller;

use Blog\Model\PostRepositoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Model\ViewModel;
use InvalidArgumentException;
use Laminas\Session\Container as SessionContainer;
use Laminas\Mvc\View\Http\ViewManager;
use Session\ServiceInterface as SessionServiceInterface;

abstract class BaseController extends AbstractActionController
{
    const DEFAULT_LAYOUT = 'layout/backend';

    /**
     * @var SessionContainer
     */
    protected $sessionContainer;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ApplicationInterface
     */
    protected $app;

    /**
     * BaseController constructor.
     * @param ServiceManager $container
     */
    public function __construct(ServiceManager $container)
    {
        $this->container = $container;


    }

    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->app = $e->getApplication();
        $viewModel = $e->getViewModel();
        $viewModel->setTemplate(self::DEFAULT_LAYOUT);

        $routeMatch = $e->getRouteMatch();

        if ($routeMatch && $routeMatch->getParam('action') !== 'login' && !$this->getSessionService()->isAdminLogged()) {
            $this->redirect()->toRoute('backend/login');
        }

        // whether admin is logged or not
        $this->layout()->setVariables([
            'isAdminLogged' => $this->getSessionService()->isAdminLogged(),
            'admin' => $this->getSessionService()->getAdmin(),
        ]);

        return parent::onDispatch($e);
    }

    /**
     * @param string $service
     * @return object
     */
    public function getService(string $service): object
    {
        if (!$this->container->has($service)) {
            $message = sprintf("Service %s was not found in container", $service);
            throw new ServiceNotFoundException($message);
        }

        return $this->container->get($service);
    }

    /**
     * @return SessionServiceInterface
     */
    public function getSessionService(): object
    {
        return $this->getService(SessionServiceInterface::class);
    }

}