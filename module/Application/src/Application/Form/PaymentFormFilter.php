<?php

namespace Application\Form;

use Zend\InputFilter;

class PaymentFormFilter extends InputFilter\InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'description',
            'required' => true,
        ])->add([
            'name' => 'installments',
            'required' => true,
            'validators' => [
                ['name' => 'digits'],
            ]
        ])->add([
            'name' => 'interest',
            'required' => false,
            'validators' => [
                [
                    'name' => 'regex',
                    'options' => [
                        'pattern' => '/^\d+(\.\d{1,2}){0,1}$/',
                    ]
                ],
            ]
        ]);
    }
}
