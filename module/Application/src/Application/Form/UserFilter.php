<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class UserFilter extends InputFilter
{

    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function __construct($serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        $this->add(array(
            'name' => 'name',
            'required' => true,
        ))->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array('name' => 'email_address'),
                array(
                    'name' => 'Application\Validator\UniqueEntry',
                    'options' => array(
                        'table' => $this->getServiceLocator()->get('Application\Model\UsersTable'),
                        'fields' => array('email'),
                    ),
                )
            )
        ))->add(array(
            'name' => 'group_id',
            'required' => true,
            'validadors'=> array(
                array(
                    'name' => 'Application\Validator\Exists',
                    'options' => array(
                        'identifier' => 'Group',
                        'table' => $this->getServiceLocator()->get('Application\Model\GroupsTable'),
                    )
                )
            )
        ))/*->add(array(
            'name' => 'password',
            'required' => true,
        ))->add(array(
            'name' => 'confirmation',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Application\Validator\PasswordConfirmation',
                    'options' => array(
                        'field' => 'password'
                    ),
                ),
            )
        ))*/;
    }
}
