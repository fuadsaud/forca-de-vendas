<?php

namespace Application\Form;

use Zend\Form\Form;

class AddressForm extends Form
{
    public function __construct($name = 'address')
    {
        parent::__construct($name);

        $this->add([
            'name' => 'type',
        ])->add([
            'name' => 'street',
        ])->add([
            'name' => 'number',
        ])->add([
            'name' => 'zipcode',
        ])->add([
            'name' => 'complement',
        ])->add([
            'name' => 'neighborhood',
        ])->add([
            'name' => 'city',
        ])->add([
            'name' => 'state',
        ])->add([
            'name' => 'country',
        ])->add([
            'name' => 'client_id',
        ]);
    }
}
