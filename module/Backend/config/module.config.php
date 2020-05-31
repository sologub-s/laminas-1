<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 23.05.2020
 * Time: 21:40
 */

namespace Backend;

use Backend\Form\LoginForm;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

return [
    // This lines opens the configuration for the RouteManager
    'router' => [
        'routes' => [
            'backend' => [
                'type' => Literal::class, // does not allow path variables like /param1/value1/param2/value2 etc.
                'options' => [
                    'route'    => '/backend',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'dashboard',
                    ],
                ],
                'may_terminate' => true, // whether the route may match by itself, if none of child routes matched
                // relative to the parent
                'child_routes'  => [
                    'login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/login',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/logout',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'logout',
                            ],
                        ],
                    ],

                    // Author
                    'author' => [
                        'type' => Literal::class, // does not allow path variables like /param1/value1/param2/value2 etc.
                        'options' => [
                            'route'    => '/author',
                        ],
                        'may_terminate' => false, // whether the route may match by itself, if none of child routes matched
                        'child_routes'  => [
                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/list',
                                    'defaults' => [
                                        'controller' => Controller\AuthorController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                            ],
                            'add' => [
                                'type' => Literal::class, // allows path variables like /param1/value1/param2/value2 etc.
                                'options' => [
                                    'route'    => '/add',
                                    'defaults' => [
                                        'controller' => Controller\AuthorController::class,
                                        'action'     => 'form',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => Segment::class, // allows path variables like /param1/value1/param2/value2 etc.
                                'options' => [
                                    'route'    => '/edit/:id',
                                    'defaults' => [
                                        'controller' => Controller\AuthorController::class,
                                        'action'     => 'form',
                                    ],
                                    'constraints' => [
                                        'id' => '[1-9]\d*',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => Segment::class, // allows path variables like /param1/value1/param2/value2 etc.
                                'options' => [
                                    'route'    => '/delete/:id',
                                    'defaults' => [
                                        'controller' => Controller\AuthorController::class,
                                        'action'     => 'delete',
                                    ],
                                    'constraints' => [
                                        'id' => '[1-9]\d*',
                                    ],
                                ],
                            ],
                        ]
                    ],

                    // Book
                    'book' => [
                        'type' => Literal::class, // does not allow path variables like /param1/value1/param2/value2 etc.
                        'options' => [
                            'route'    => '/book',
                        ],
                        'may_terminate' => false, // whether the route may match by itself, if none of child routes matched
                        'child_routes'  => [
                            'list' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route'    => '/list',
                                    'defaults' => [
                                        'controller' => Controller\BookController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                            ],
                            'add' => [
                                'type' => Literal::class, // allows path variables like /param1/value1/param2/value2 etc.
                                'options' => [
                                    'route'    => '/add',
                                    'defaults' => [
                                        'controller' => Controller\BookController::class,
                                        'action'     => 'form',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => Segment::class, // allows path variables like /param1/value1/param2/value2 etc.
                                'options' => [
                                    'route'    => '/edit/:id',
                                    'defaults' => [
                                        'controller' => Controller\BookController::class,
                                        'action'     => 'form',
                                    ],
                                    'constraints' => [
                                        'id' => '[1-9]\d*',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type' => Segment::class, // allows path variables like /param1/value1/param2/value2 etc.
                                'options' => [
                                    'route'    => '/delete/:id',
                                    'defaults' => [
                                        'controller' => Controller\BookController::class,
                                        'action'     => 'delete',
                                    ],
                                    'constraints' => [
                                        'id' => '[1-9]\d*',
                                    ],
                                ],
                            ],
                        ]
                    ],

                ],
            ],
        ],
    ],
    'service_manager' => [

        'aliases' => [
            /*
            //Model\PostRepositoryInterface::class => Model\PostRepository::class,
            Model\PostRepositoryInterface::class => Model\LaminasDbSqlRepository::class,
            //Model\PostCommandInterface::class => Model\PostCommand::class,
            Model\PostCommandInterface::class => Model\LaminasDbSqlCommand::class,
            */
        ],
        'factories' => [
            //Model\PostRepository::class => InvokableFactory::class, // because factory has no dependencies by itself
            LoginForm::class => ReflectionBasedAbstractFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => ReflectionBasedAbstractFactory::class, // automatically inject dependencies
            Controller\AuthorController::class => ReflectionBasedAbstractFactory::class, // automatically inject dependencies
            Controller\BookController::class => ReflectionBasedAbstractFactory::class, // automatically inject dependencies
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'layout/backend'           => __DIR__ . '/../view/layout/backend.phtml',
            //'error/404'               => __DIR__ . '/../view/error/404.phtml',
            //'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
    ],

    'navigation' => [
        'backend' => [
            [
                'label' => 'Dashboard',
                'route' => 'backend',
            ],

            // Author
            [
                'label' => 'Author',
                'route' => 'backend/author/list',
                'pages' => [
                    [
                        'label' => 'Add Author',
                        'route' => 'backend/author/add',
                    ],
                    [
                        'label' => 'Edit Author',
                        'route' => 'backend/author/edit',
                    ],
                ],
            ],

            // Book
            [
                'label' => 'Book',
                'route' => 'backend/book/list',
                'pages' => [
                    [
                        'label' => 'Add Book',
                        'route' => 'backend/book/add',
                    ],
                    [
                        'label' => 'Edit Book',
                        'route' => 'backend/book/edit',
                    ],
                ],
            ],

            [
                'label' => 'Logout',
                'route' => 'backend/logout',
            ],
        ],
    ],
];