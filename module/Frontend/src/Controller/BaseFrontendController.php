<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:55
 */

namespace Frontend\Controller;

use Laminas\Mvc\MvcEvent;
use App\Controller\BaseActionController;

/**
 * Class BaseController
 * @package Frontend\Controller
 */
abstract class BaseFrontendController extends BaseActionController
{
    const DEFAULT_LAYOUT = 'layout/frontend';

    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $e)
    {
        // setting layout to self::DEFAULT_LAYOUT
        $viewModel = $e->getViewModel();
        $viewModel->setTemplate($this->getDefaultLayout());

        // set global variables
        $this->layout()->setVariables([

        ]);

        return parent::onDispatch($e);
    }

}