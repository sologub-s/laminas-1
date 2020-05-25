<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:55
 */

namespace Backend\Controller;

use Laminas\Mvc\MvcEvent;
use App\Controller\BaseActionController;

/**
 * Class BaseController
 * @package Backend\Controller
 */
abstract class BaseBackendController extends BaseActionController
{
    const DEFAULT_LAYOUT = 'layout/backend';

    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $e)
    {
        // setting layout to self::DEFAULT_LAYOUT
        $viewModel = $e->getViewModel();
        $viewModel->setTemplate($this->getDefaultLayout());

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

}