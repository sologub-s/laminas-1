<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 21:43
 */

namespace Blog\Controller;

use Blog\Model\PostRepositoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use InvalidArgumentException;
use Laminas\Session\Container as SessionContainer;

class ListController extends AbstractActionController
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var SessionContainer
     */
    private $sessionContainer;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(PostRepositoryInterface $postRepository/*, $dbAdapter*/, SessionContainer $sessionContainer, ContainerInterface $container)
    {
        $this->postRepository = $postRepository;
        $this->sessionContainer = $sessionContainer;
        $this->container = $container;
    }

    public function indexAction()
    {
        //$this->sessionContainer->album = 'I got a new CD with awesome music. zxc';
        //var_dump($this->sessionContainer->album);
        //var_dump($this->container->get(SessionContainer::class)->album);
        //die();

        return new ViewModel([
            'posts' => $this->postRepository->findAllPosts(),
        ]);
    }

    public function detailAction()
    {
        $id = $this->params()->fromRoute('id'); // get path param, i.e. /id/21

        try {
            $post = $this->postRepository->findPost($id);
        } catch (InvalidArgumentException $ex) {
            return $this->redirect()->toRoute('blog');
        }

        // we can simply return an array, in the case of this it will be automatically wrapped into ViewModel
        return new ViewModel([
            'post' => $post,
        ]);
    }
}