<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 24.05.2020
 * Time: 15:56
 */

namespace Frontend\Controller;

/**
 * Class IndexController
 * @package Frontend\Controller
 */
class IndexController extends BaseFrontendController
{
    public function indexAction()
    {
        $this->layout()->setVariables([
            'zxc' => 'ZXC_FRONTEND_variable',
        ]);

        return [
            //'posts' => $this->postRepository->findAllPosts(),
        ];
    }

    public function aaaAction()
    {
        $this->layout()->setVariables([
            'zxc' => 'ZXC_FRONTEND_variable on Aaa page',
        ]);

        return [
            //'posts' => $this->postRepository->findAllPosts(),
        ];
    }
}