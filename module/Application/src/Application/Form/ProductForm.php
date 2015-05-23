<?php

namespace Application\Form;

use Zend\Form\Form;

class ProductForm extends Form
{
    public function __construct($name = 'product')
    {
        parent::__construct($name);

        $this->init();
    }

    public function init()
    {
        $this->add([
            'name' => 'name'
        ])->add([
            'name' => 'description'
        ])->add([
            'name' => 'price'
        ])->add([
            'name' => 'stock_quantity'
        ])->add([
            'name' => 'categories',
        ]);
    }
}

