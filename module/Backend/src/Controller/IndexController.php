<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:56
 */

namespace Backend\Controller;

use Blog\Model\PostRepositoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Backend\Controller\BaseController;
use Laminas\View\Model\ViewModel;
use InvalidArgumentException;
use Laminas\Session\Container as SessionContainer;
use Session\ServiceInterface as SessionServiceInterface;
use Backend\Form\LoginForm;

class IndexController extends BaseController
{
    public function dashboardAction()
    {
        //$this->sessionContainer->album = 'I got a new CD with awesome music. zxc';
        //var_dump($this->sessionContainer->album);
        //var_dump($this->container->get(SessionContainer::class)->album);
        //die();

        $this->layout()->setVariables([
            'zxc' => 'ZXC_variable',
        ]);

        return [
            //'posts' => $this->postRepository->findAllPosts(),
        ];
    }

    public function loginAction()
    {
        $request   = $this->getRequest();

        /** @var LoginForm $form */
        $form = $this->container->get(LoginForm::class);
        $form->init();

        $viewModel = [
            'form' => $form,
            'errorMessage' => '',
        ];

        if (! $request->isPost()) {
            return $viewModel;
        }

        $form->setData($request->getPost());

        $form->isValid();

        /*
        if (! $this->form->isValid()) {
            return $viewModel;
        }
        */

        /*
        $data = $this->form->getData()['post'];
        $post = new Post($data['title'], $data['text']);
        */
        $password = $form->getData()['password'];

        if(!$this->getSessionService()->adminLogin($password)) {
            //$form->getElements();
            $viewModel['errorMessage'] = 'Wrong password';
            return $viewModel;
        }

        return $this->redirect()->toRoute(
            'backend'
        );
    }

    public function logoutAction()
    {
        $this->getSessionService()->adminLogout();

        return $this->redirect()->toRoute(
            'backend'
        );
    }
}