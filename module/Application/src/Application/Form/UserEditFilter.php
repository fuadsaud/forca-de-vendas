<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class UserEditFilter extends InputFilter
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
                        'ignoreFields' => array('users.id' => 'id'),
                    ),
                )
            )
        ))->add(array(
            'name' => 'confirmation',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'Application\Validator\PasswordConfirmation',
                    'options' => array(
                        'field' => 'password'
                    ),
                ),
            )
        ));
    }

    public function isValid($context = null)
    {
        $data = $this->getRawValues();
        if (!is_null($data['password'])) {
            $this->inputs['confirmation']->setRequired(true);
        }

        return parent::isValid($context);
    }
}
