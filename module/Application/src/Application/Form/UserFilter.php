<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class UserFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'name',
            'required' => true,
        ))->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array('name' => 'email_address')
            )
        ))->add(array(
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
        ));
    }
}
