<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function loginAction()
    {
        $table = $this->getServiceLocator()->get('Application\Model\UsersTable');
        $form = $table->getForm('login');
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $result = $table->authenticate($form->getData());
                $messages = $result->getMessages();
                if ($result->getCode() == Result::SUCCESS) {
                    $this->redirect()->toRoute('home');
                } else {
                    $form->get('password')->setValue('');
                    $this->layout()->messages = [['type' => 'error', 'text' => current($messages)]];
                }
            }
        }

        $this->layout('layout/unlogged-layout');

        return new ViewModel(['form' => $form]);
    }

    public function logoutAction()
    {
        $serviceLocator = $this->getServiceLocator();
        $serviceLocator->get('ApplicationAuth')->clearIdentity();

        return $this->redirect()->toRoute('login');
    }

    public function changePasswordAction()
    {
        return new ViewModel();
    }
}
