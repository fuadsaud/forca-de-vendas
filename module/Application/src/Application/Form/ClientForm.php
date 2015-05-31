<?php

namespace Application\Form;

use Zend\Form;

class ClientForm extends Form\Form
{
    public function __construct($name = 'client')
    {
        parent::__construct($name);
        $this->init();
    }

    public function init()
    {
        $this->add(array(
            'name' => 'name'
        ))->add(array(
            'name' => 'email',
        ))->add(array(
            'name' => 'cnpj',
        ))->add(array(
            'name' => 'trading_name',
        ))->add(array(
            'name' => 'addresses',
            'type' => 'collection',
            'options' => array(
                 'count' => 2,
                 'allow_add' => false,
                 'target_element' => array(
                     'type' => 'Application\Form\AddressForm',
                 ),
             ),
        ));
    }
}
