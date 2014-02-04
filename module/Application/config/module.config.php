<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
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
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Application' => 'Application\Controller\ApplicationController',
            'Application\Controller\DevelopmentMode' => 'Application\Controller\DevelopmentModeController',
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
    ),
    // Console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'create-api-module' => array(
                    'options' => array(
                        'route' => 'build api module',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Application',
                            'action'     => 'apiModule',
                        ),
                    ),
                ),
                'create-api' => array(
                    'options' => array(
                        'route' => 'build api',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Application',
                            'action'     => 'api',
                        ),
                    ),
                ),
                'development-disable' => array(
                    'options' => array(
                        'route' => 'development disable',
                        'defaults' => array(
                            'controller' => 'Application\Controller\DevelopmentMode',
                            'action'     => 'disable',
                        ),
                    ),
                ),
                'development-enable' => array(
                    'options' => array(
                        'route' => 'development enable',
                        'defaults' => array(
                            'controller' => 'Application\Controller\DevelopmentMode',
                            'action'     => 'enable',
                        ),
                    ),
                ),
                'drop' => array(
                    'options' => array(
                        'route' => 'drop',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Application',
                            'action'     => 'drop',
                        ),
                    ),
                ),

            ),
        ),
    ),
);
