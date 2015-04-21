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
        $this->add(array(
            'name' => 'username',
        ))
        ->add(array(
            'name' => 'password',
            'type' => 'password',
        ));
    }
}
