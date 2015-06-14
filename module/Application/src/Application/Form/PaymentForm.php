<?php

namespace Application\Form;

use Zend\Form\Form;

class PaymentForm extends Form
{

    public function __construct($name = 'payment')
    {
        parent::__construct($name);
        $this->add([
            'name' => 'name',
        ])->add([
            'name' => 'forms',
            'type' => 'collection',
            'options' => [
                'count' => 1,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Application\Form\PaymentFormFieldset',
                ),
            ]
        ]);
    }
}
