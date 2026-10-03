<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'zfcuser' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'ZfcUser/layout'             => __DIR__ . '/../view/layout/layout.phtml',
            'ZfcUser/header'             => __DIR__ . '/../view/partial/header.phtml',
            'ZfcUser/footer'             => __DIR__ . '/../view/partial/footer.phtml',
            'zfc-user/user/login'        => __DIR__ . '/../view/zfc-user/user/login.phtml',
            'zfc-user/user/index'        => __DIR__ . '/../view/zfc-user/user/index.phtml',
            'zfc-user/user/changeinformation'      => __DIR__ . '/../view/zfc-user/user/changeinformation.phtml',
            'zfc-user/user/changepassword'      => __DIR__ . '/../view/zfc-user/user/changepassword.phtml',
            'zfc-user/user/myads'        => __DIR__ . '/../view/zfc-user/user/myads.phtml',
            'zfc-user/user/mycar'        => __DIR__ . '/../view/zfc-user/user/mycar.phtml',
            'zfc-user/user/myhotel'      => __DIR__ . '/../view/zfc-user/user/myhotel.phtml',
            'zfc-user/user/mypub'        => __DIR__ . '/../view/zfc-user/user/mypub.phtml',
            'zfc-user/user/myblog'       => __DIR__ . '/../view/zfc-user/user/myblog.phtml',
            'zfc-user/user/mystore'      => __DIR__ . '/../view/zfc-user/user/mystore.phtml',
            'zfc-user/user/changemypass' => __DIR__ . '/../view/zfc-user/user/changemypass.phtml',
            'zfc-user/store/produits'    => __DIR__ . '/../view/zfc-user/store/produits.phtml',
            'zfc-user/store/categories'  => __DIR__ . '/../view/zfc-user/store/categories.phtml',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'zfcuser' => 'ZfcUser\Controller\UserController',
            'store' => 'ZfcUser\Controller\StoreController',
        ),
    ),
    'service_manager' => array(
        'aliases' => array(
            'zfcuser_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        ),
    ),
    'view_helper_config' => array(
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        )
    ),
    'router' => array(
        'routes' => array(
            'zfcuser' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/user',
                    'defaults' => array(
                        'controller' => 'zfcuser',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(

                    'thawourth26' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/thawourth26',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'login',
                            ),
                        ),
                    ),
                    'authenticate' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/authenticate',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'authenticate',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                    'login-json' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login-json',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'login-json',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'register',
                            ),
                        ),
                    ),
                    'changepassword' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-password',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'changepassword',
                            ),
                        ),
                    ),
                    'changeemail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-email',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action' => 'changeemail',
                            ),
                        ),
                    ),
                    'changeinformation' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-information',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'changeinformation',
                            ),
                        ),
                    ),

                    'myads' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/myads',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'myads',
                            ),
                        ),
                    ),
                    'mycar' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/mycar',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'mycar',
                            ),
                        ),
                    ),
                    'myhotel' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/myhotel',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'myhotel',
                            ),
                        ),
                    ),
                    'mypub' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/mypub',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'mypub',
                            ),
                        ),
                    ),
                    'myblog' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/myblog',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'myblog',
                            ),
                        ),
                    ),
                    'mystore' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/mystore',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'mystore',
                            ),
                        ),
                    ),
                    'produits' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/produits',
                            'defaults' => array(
                                'controller' => 'store',
                                'action'     => 'produits',

                            ),
                        ),
                    ),
                    'categories' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/categories',
                            'defaults' => array(
                                'controller' => 'store',
                                'action'     => 'categories',

                            ),
                        ),
                    ),
                    'categories-json' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/categories-json',
                            'defaults' => array(
                                'controller' => 'store',
                                'action'     => 'categories-json',

                            ),
                        ),
                    ),
                    'add-produit-json' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/add-produit-json',
                            'defaults' => array(
                                'controller' => 'store',
                                'action'     => 'add-produit-json',
                            ),
                        ),
                    ),
                    'add-category-json' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/add-category-json',
                            'defaults' => array(
                                'controller' => 'store',
                                'action'     => 'add-category-json',
                            ),
                        ),
                    ),
                    'produits-json' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/produits-json',
                            'defaults' => array(
                                'controller' => 'store',
                                'action'     => 'produits-json',
                            ),
                        ),
                    ),
                    'changemypass' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/changemypass',
                            'defaults' => array(
                                'controller' => 'zfcuser',
                                'action'     => 'changemypass',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
