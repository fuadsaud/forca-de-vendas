<?php

namespace Application\Form;

use Zend\Form\Fieldset;

class OrderItemFieldset extends Fieldset
{
    public function __construct($name = 'order_item')
    {
        parent::__construct($name);
        $this->add([
            'name' => 'product_id',
        ])->add([
            'name' => 'quantity',
        ])->add([
            'name' => 'price_id',
        ]);
    }
}
