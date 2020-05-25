<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:56
 */

namespace Backend\Controller;

use Backend\Form\LoginForm;

/**
 * Class IndexController
 * @package Backend\Controller
 */
class IndexController extends BaseBackendController
{
    public function dashboardAction()
    {
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

        $form = new LoginForm();
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