<?php

namespace Application\Form;

use Zend\Form\Form;

class OrderForm extends Form
{
    public function __construct($name = 'order')
    {
        parent::__construct($name);
        $this->add([
            'name' => 'client_id',
        ])->add([
            'name' => 'charge_address_id',
        ])->add([
            'name' => 'deliver_address_id',
        ])->add([
            'name' => 'payment_form_id',
        ])->add([
            'name' => 'items',
            'type' => 'collection',
            'options' => [
                'count' => 1,
                'allow_add' => true,
                'target_element' => [
                    'type' => 'Application\Form\OrderItemFieldset',
                ]
            ]
        ]);
    }
}
