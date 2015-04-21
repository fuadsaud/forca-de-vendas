<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class UserEditFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'name',
            'required' => false,
        ))->add(array(
            'name' => 'email',
            'required' => false,
            'validators' => array(
                array('name' => 'email_address')
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
        $passed = false;
        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                $passed = true;
                break;
            }
        }

        if (!$passed) {
            foreach ($this->inputs as $input) {
                $input->setRequired(true);
            }
        } else {
            if (!is_null($data['password'])) {
                $this->inputs['confirmation']->setRequired(true);
            }
        }

        return parent::isValid($context);
    }
}
