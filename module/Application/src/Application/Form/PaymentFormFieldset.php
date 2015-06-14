<?php

namespace Application\Form;

use Zend\Form\Fieldset;

class PaymentFormFieldset extends Fieldset
{

    public function __construct($name = 'payment_form')
    {
        parent::__construct($name);
        $this->add([
            'name' => 'description',
        ])->add([
            'name' => 'installments',
        ])->add([
            'name' => 'interest',
        ]);
    }
}
