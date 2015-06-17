<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'change_password' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/change-password',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'change-password',
                    ),
                ),
            ),
            'addresses' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/api/clients/:cliend_id/addresses[/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Api\Controller',
                        'controller' => 'addresses',
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+',
                        'client_id' => '[0-9]+',
                    ),
                ),
            ),
            'api_password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/api-change-password/:id/:hash',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Api\Controller',
                        'controller' => 'Users',
                        'action' => 'change-password',
                    ),
                ),
            ),
            'api' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/api/:controller[/:id[/:action]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Api\Controller',
                    ),
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'pt_BR',
        'translation_file_patterns' => array(
            array(
                'type' => 'phparray',
                'base_dir' => __DIR__.'/../language',
                'pattern' => '%s.php',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Products',
                'uri' => '#/products'
            ),
            array(
                'label' => 'Basket',
                'uri' => '#/basket'
            ),
            array(
                'label' => 'Admin',
                'uri' => '#',
                'pages' => array(
                    array(
                        'label' => 'Users',
                        'uri' => '#/adm/users',
                        'permissions' => array('identifier' => 'users', 'method' => 'retrieve_list'),
                    ),
                    array(
                        'label' => 'Clients',
                        'uri' => '#/adm/clients/list',
                        'permissions' => array('identifier' => 'clients', 'method' => 'retrieve_list'),
                    ),
                    array(
                        'label' => 'Products',
                        'uri' => '#/adm/products',
                        'permissions' => array('identifier' => 'products', 'method' => 'retrieve_list'),
                    ),
                    array(
                        'label' => 'Payments',
                        'uri' => '#/adm/payments',
                        'permissions' => array('identifier' => 'payments', 'method' => 'retrieve_list'),
                    ),
                    array(
                        'label' => 'Categories',
                        'uri' => '#/adm/categories',
                        'permissions' => array('identifier' => 'categories', 'method' => 'retrieve_list'),
                    ),
                ),
            ),
            array(
                'label' => 'Relatórios',
                'uri' => '#/adm/orders',
                'permissions' => array('identifier' => 'reports', 'method' => 'retrieve_list'),
            ),
        )
    ),
);
