<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'Application\Api\Controller\Users' => function($sm) {
                    $users = $sm->getServiceLocator()->get('Application\Model\UsersTable');
                    return new Controller\ApiController($users, 'User');
                }
            )
        );
    }


    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\UsersTable' => function($sm) {
                    $table = new TableGateway('users', $sm->get('Zend\Db\Adapter\Adapter'));
                    return new Model\UsersTable($table);
                },
                'Application\Model\GroupsTable' => function($sm) {
                    $table = new TableGateway('groups', $sm->get('Zend\Db\Adapter\Adapter'));
                    return new Model\GroupsTable($table);
                }
            ),
        );
    }
}
