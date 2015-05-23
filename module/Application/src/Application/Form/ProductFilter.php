<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;

class ProductFilter extends InputFilter
{

    public function __construct()
    {
        $this->add([
            'name' => 'name',
            'required' => true,
        ])->add([
            'name' => 'description',
            'required' => true,
        ])->add([
            'name' => 'price',
            'required' => true,
            'validators' => [
                ['name' => 'Zend\I18n\Validator\IsFloat']
            ],
        ])->add([
            'name' => 'stock_quantity',
            'required' => true,
        ])->add([
            'name' => 'categories',
            'required' => true,
        ]);
    }
}
