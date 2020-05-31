<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 25.05.2020
 * Time: 15:45
 */

namespace App\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Session\ServiceInterface as SessionServiceInterface;

/**
 * Class BaseActionController
 * @package App\Controller
 */
abstract class BaseActionController extends AbstractActionController
{
    /**
     * @param string $service
     * @return object
     */
    public function getService(string $service): object
    {
        if (!\App::has($service)) {
            $message = sprintf("Service %s was not found in container", $service);
            throw new ServiceNotFoundException($message);
        }

        return \App::get($service);
    }

    /**
     * @return string
     */
    public function getDefaultLayout(): string
    {
        return static::DEFAULT_LAYOUT;
    }

    /**
     * @return SessionServiceInterface
     */
    public function getSessionService(): object
    {
        return self::getService(SessionServiceInterface::class);
    }

    /**
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function renderPartial(string $template, array $variables = []): string
    {
        $viewModel = new ViewModel($this->layout()->getVariables()->getArrayCopy());
        $viewModel->setVariables($variables);
        $viewModel->setTemplate($template);
        $renderer = \App::get(PhpRenderer::class);
        return $renderer->render($viewModel);
    }

}