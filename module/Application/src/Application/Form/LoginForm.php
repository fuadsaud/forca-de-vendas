<?php

namespace Application\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = 'login')
    {
        parent::__construct($name);
        $this->init();
    }

    public function init()
    {
        $this->setAttribute('method', 'post')
             ->setAttribute('role', 'form')
             ->setAttribute('class', 'form-signin');

        $this->add(array(
            'name' => 'email',
            'options' => array(
                // 'label' => 'Email',
            ),
            'attributes' => array(
                'type' => 'email',
                'required' => 'required',
                'autofocus' => 'true',
                'placeholder' => 'Email address',
                'class' => 'form-control'
            )
        ))->add(array(
            'name' => 'password',
            'options' => array(
            ),
            'attributes' => array(
                'type' => 'password',
                'required' => 'required',
                'placeholder' => 'Password',
                'class' => 'form-control'
            )
        ));
    }
}
